<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use Throwable;

class EnsureSubscribed
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user === null) {
            return $next($request);
        }

        if ((string) $user->role === UserRole::SUPER_ADMIN) {
            return $next($request);
        }

        $store = $user->store;
        if ($store !== null && $store->isSubscribed()) {
            return $next($request);
        }

        if ($store !== null && $store->stripe_subscription_id !== null) {
            $stripeSecret = (string) config('services.stripe.secret');
            if ($stripeSecret !== '') {
                $synced = cache()->add('stripe_subscription_sync_store_'.$store->id, true, 120);
                if ($synced) {
                    try {
                        $stripe = new StripeClient($stripeSecret);
                        $subscription = $stripe->subscriptions->retrieve($store->stripe_subscription_id, []);

                        $store->subscription_status = (string) ($subscription->status ?? $store->subscription_status);
                        $store->stripe_customer_id = $subscription->customer ?? $store->stripe_customer_id;

                        if (isset($subscription->current_period_end)) {
                            $store->subscription_ends_at = CarbonImmutable::createFromTimestamp((int) $subscription->current_period_end);
                        }

                        $store->save();
                    } catch (Throwable $e) {
                    }
                }
            }

            $store->refresh();
            if ($store->isSubscribed()) {
                return $next($request);
            }
        }

        return redirect()->route('pricing')->with('error', 'Tu membresía no está activa. Elige un plan para continuar.');
    }
}
