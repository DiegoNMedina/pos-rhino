<?php

namespace App\Http\Controllers\Billing;

use App\Models\Store;
use App\Models\SubscriptionPayment;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeWebhookController
{
    public function __invoke(Request $request)
    {
        $secret = (string) config('services.stripe.webhook_secret');
        if ($secret === '') {
            return response('Stripe webhook no configurado.', 500);
        }

        $payload = (string) $request->getContent();
        $sigHeader = (string) $request->header('Stripe-Signature');

        $event = Webhook::constructEvent($payload, $sigHeader, $secret);

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            $storeId = $session->client_reference_id ?? ($session->metadata->store_id ?? null);
            if ($storeId !== null) {
                $store = Store::query()->whereKey((int) $storeId)->first();
                if ($store) {
                    $store->billing_method = 'stripe';
                    $store->subscription_status = 'active';
                    $store->plan = (string) ($session->metadata->plan ?? $store->plan);
                    $store->stripe_customer_id = $session->customer ?? $store->stripe_customer_id;
                    $store->stripe_subscription_id = $session->subscription ?? $store->stripe_subscription_id;

                    $this->syncSubscriptionPeriod($store);
                    $store->save();
                }
            }
        }

        if ($event->type === 'customer.subscription.updated' || $event->type === 'customer.subscription.deleted') {
            $subscription = $event->data->object;

            $storeId = $subscription->metadata->store_id ?? null;
            if ($storeId !== null) {
                $store = Store::query()->whereKey((int) $storeId)->first();
                if ($store) {
                    $store->billing_method = 'stripe';
                    $store->stripe_customer_id = $subscription->customer ?? $store->stripe_customer_id;
                    $store->stripe_subscription_id = $subscription->id ?? $store->stripe_subscription_id;

                    if ($event->type === 'customer.subscription.deleted') {
                        $store->subscription_status = 'canceled';
                    } else {
                        $store->subscription_status = (string) ($subscription->status ?? $store->subscription_status);
                        $store->plan = (string) ($subscription->metadata->plan ?? $store->plan);
                    }

                    $periodEnd = isset($subscription->current_period_end) ? CarbonImmutable::createFromTimestamp((int) $subscription->current_period_end) : null;
                    $store->subscription_ends_at = $periodEnd;

                    $store->save();
                }
            }
        }

        if ($event->type === 'invoice.paid' || $event->type === 'invoice.payment_failed') {
            $invoice = $event->data->object;
            $customerId = $invoice->customer ?? null;
            $subscriptionId = $invoice->subscription ?? null;

            $store = Store::query()
                ->when($customerId !== null, fn ($q) => $q->where('stripe_customer_id', $customerId))
                ->when($customerId === null && $subscriptionId !== null, fn ($q) => $q->where('stripe_subscription_id', $subscriptionId))
                ->first();

            if ($store) {
                $lines = $invoice->lines->data ?? [];
                $lineStarts = [];
                $lineEnds = [];

                foreach ($lines as $line) {
                    $start = $line->period->start ?? null;
                    $end = $line->period->end ?? null;

                    if ($start !== null) {
                        $lineStarts[] = (int) $start;
                    }
                    if ($end !== null) {
                        $lineEnds[] = (int) $end;
                    }
                }

                $linePeriodStart = $lineStarts !== [] ? min($lineStarts) : null;
                $linePeriodEnd = $lineEnds !== [] ? max($lineEnds) : null;

                $periodStartRaw = $linePeriodStart ?? ($invoice->period_start ?? null);
                $periodEndRaw = $linePeriodEnd ?? ($invoice->period_end ?? null);

                $periodStart = $periodStartRaw !== null ? CarbonImmutable::createFromTimestamp((int) $periodStartRaw) : null;
                $periodEnd = $periodEndRaw !== null ? CarbonImmutable::createFromTimestamp((int) $periodEndRaw) : null;

                SubscriptionPayment::query()->updateOrCreate(
                    [
                        'provider' => 'stripe',
                        'reference_id' => (string) ($invoice->id ?? ''),
                    ],
                    [
                        'store_id' => $store->id,
                        'event_type' => $event->type,
                        'status' => $event->type === 'invoice.paid' ? 'paid' : 'failed',
                        'currency' => isset($invoice->currency) ? strtoupper((string) $invoice->currency) : null,
                        'amount' => isset($invoice->amount_paid) ? ((float) $invoice->amount_paid / 100) : null,
                        'period_start_at' => $periodStart,
                        'period_end_at' => $periodEnd,
                        'payload' => json_decode($payload, true),
                    ]
                );

                if ($event->type === 'invoice.paid') {
                    $store->subscription_status = 'active';
                } else {
                    $store->subscription_status = 'past_due';
                }

                $store->billing_method = 'stripe';
                $store->stripe_customer_id = $customerId ?? $store->stripe_customer_id;
                $store->stripe_subscription_id = $subscriptionId ?? $store->stripe_subscription_id;

                if ($event->type === 'invoice.paid') {
                    $store->subscription_ends_at = $periodEnd;
                    $this->syncSubscriptionPeriod($store);
                }

                $store->save();
            }
        }

        return response()->json(['received' => true]);
    }

    private function syncSubscriptionPeriod(Store $store): void
    {
        $secret = (string) config('services.stripe.secret');
        if ($secret === '' || $store->stripe_subscription_id === null) {
            return;
        }

        $stripe = new StripeClient($secret);
        $subscription = $stripe->subscriptions->retrieve($store->stripe_subscription_id, []);

        $store->subscription_status = (string) ($subscription->status ?? $store->subscription_status);

        if (isset($subscription->current_period_end)) {
            $store->subscription_ends_at = CarbonImmutable::createFromTimestamp((int) $subscription->current_period_end);
        }
    }
}
