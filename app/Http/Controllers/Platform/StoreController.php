<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $stores = Store::query()
            ->when($q !== '', function ($builder) use ($q) {
                $builder->where(function ($inner) use ($q) {
                    $inner
                        ->where('name', 'like', '%'.$q.'%')
                        ->orWhere('code', 'like', '%'.$q.'%');
                });
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('platform.stores.index', [
            'stores' => $stores,
            'q' => $q,
        ]);
    }

    public function edit(Store $store)
    {
        return view('platform.stores.edit', [
            'store' => $store,
        ]);
    }

    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50'],
            'is_active' => ['required', 'boolean'],
            'plan' => ['required', 'in:starter,pro,enterprise'],
            'subscription_status' => ['required', 'in:inactive,trialing,active,past_due,canceled'],
            'billing_method' => ['nullable', 'in:stripe,transfer'],
            'trial_ends_at' => ['nullable', 'date'],
            'subscription_ends_at' => ['nullable', 'date'],
            'stripe_customer_id' => ['nullable', 'string', 'max:120'],
            'stripe_subscription_id' => ['nullable', 'string', 'max:120'],
        ]);

        $store->fill($validated);
        $store->save();

        return redirect()->route('platform.stores.edit', $store)->with('success', 'Tienda actualizada.');
    }

    public function portal(Store $store)
    {
        $stripeSecret = (string) config('services.stripe.secret');
        if ($stripeSecret === '') {
            return back()->with('error', 'Stripe no está configurado.');
        }

        if (! $store->stripe_customer_id) {
            return back()->with('error', 'Esta tienda aún no tiene un cliente de Stripe asociado.');
        }

        $stripe = new StripeClient($stripeSecret);

        $session = $stripe->billingPortal->sessions->create([
            'customer' => $store->stripe_customer_id,
            'return_url' => route('platform.stores.edit', $store, true),
        ]);

        return redirect()->away($session->url);
    }
}
