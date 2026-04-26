<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless($user && $user->store, 403);

        $payments = SubscriptionPayment::query()
            ->where('store_id', $user->store->id)
            ->orderByDesc('id')
            ->limit(25)
            ->get();

        return view('billing.index', [
            'store' => $user->store,
            'payments' => $payments,
        ]);
    }

    public function changePlan(Request $request, string $plan)
    {
        $user = $request->user();
        abort_unless($user && $user->store, 403);

        $plan = strtolower(trim($plan));
        abort_unless(in_array($plan, ['starter', 'pro', 'enterprise'], true), 404);

        $stripeSecret = (string) config('services.stripe.secret');
        $priceId = (string) config('services.stripe.prices.'.$plan);
        if ($stripeSecret === '' || $priceId === '') {
            return back()->with('error', 'Stripe no está configurado.');
        }

        if (! $user->store->stripe_subscription_id) {
            return back()->with('error', 'Tu suscripción aún no está asociada a Stripe.');
        }

        $stripe = new StripeClient($stripeSecret);

        $subscription = $stripe->subscriptions->retrieve($user->store->stripe_subscription_id, []);
        $itemId = null;
        if (
            $subscription
            && isset($subscription->items)
            && isset($subscription->items->data)
            && isset($subscription->items->data[0])
            && isset($subscription->items->data[0]->id)
        ) {
            $itemId = $subscription->items->data[0]->id;
        }
        if (! $itemId) {
            return back()->with('error', 'No se pudo identificar el item de la suscripción.');
        }

        $stripe->subscriptions->update($user->store->stripe_subscription_id, [
            'cancel_at_period_end' => false,
            'items' => [
                [
                    'id' => $itemId,
                    'price' => $priceId,
                ],
            ],
            'proration_behavior' => 'create_prorations',
            'metadata' => [
                'store_id' => (string) $user->store->id,
                'plan' => $plan,
            ],
        ]);

        $user->store->plan = $plan;
        $user->store->billing_method = 'stripe';
        $user->store->subscription_status = 'active';
        $user->store->save();

        return back()->with('success', 'Plan actualizado.');
    }

    public function cancel(Request $request)
    {
        $user = $request->user();
        abort_unless($user && $user->store, 403);

        $stripeSecret = (string) config('services.stripe.secret');
        if ($stripeSecret === '') {
            return back()->with('error', 'Stripe no está configurado.');
        }

        if (! $user->store->stripe_subscription_id) {
            return back()->with('error', 'Tu suscripción aún no está asociada a Stripe.');
        }

        $stripe = new StripeClient($stripeSecret);

        $subscription = $stripe->subscriptions->update($user->store->stripe_subscription_id, [
            'cancel_at_period_end' => true,
        ]);

        $endsAt = isset($subscription->current_period_end)
            ? now()->setTimestamp((int) $subscription->current_period_end)
            : null;

        $user->store->subscription_status = 'canceled';
        $user->store->subscription_ends_at = $endsAt;
        $user->store->save();

        return back()->with('success', 'Tu suscripción se canceló (al finalizar el periodo).');
    }

    public function checkout(Request $request, string $plan)
    {
        $user = $request->user();
        abort_unless($user && $user->store, 403);
        abort_unless($user->store_id !== null, 403);

        $plan = strtolower(trim($plan));
        abort_unless(in_array($plan, ['starter', 'pro', 'enterprise'], true), 404);

        $stripeSecret = (string) config('services.stripe.secret');
        $priceId = (string) config('services.stripe.prices.'.$plan);
        if ($stripeSecret === '' || $priceId === '') {
            return back()->with('error', 'Stripe no está configurado.');
        }

        $stripe = new StripeClient($stripeSecret);

        $session = $stripe->checkout->sessions->create([
            'mode' => 'subscription',
            'payment_method_types' => ['card'],
            'customer_email' => $user->email,
            'client_reference_id' => (string) $user->store->id,
            'metadata' => [
                'store_id' => (string) $user->store->id,
                'plan' => $plan,
            ],
            'subscription_data' => [
                'metadata' => [
                    'store_id' => (string) $user->store->id,
                    'plan' => $plan,
                ],
            ],
            'line_items' => [
                [
                    'price' => $priceId,
                    'quantity' => 1,
                ],
            ],
            'success_url' => route('billing.success', [], true).'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('pricing', [], true),
        ]);

        return redirect()->away($session->url);
    }

    public function portal(Request $request)
    {
        $user = $request->user();
        abort_unless($user && $user->store, 403);

        $stripeSecret = (string) config('services.stripe.secret');
        if ($stripeSecret === '') {
            return back()->with('error', 'Stripe no está configurado.');
        }

        if (! $user->store->stripe_customer_id) {
            return back()->with('error', 'Tu cuenta aún no tiene un cliente de Stripe asociado.');
        }

        $stripe = new StripeClient($stripeSecret);

        $session = $stripe->billingPortal->sessions->create([
            'customer' => $user->store->stripe_customer_id,
            'return_url' => route('billing.index', [], true),
        ]);

        return redirect()->away($session->url);
    }

    public function success(Request $request)
    {
        $user = $request->user();
        abort_unless($user && $user->store, 403);

        return view('billing.success', [
            'store' => $user->store,
        ]);
    }
}
