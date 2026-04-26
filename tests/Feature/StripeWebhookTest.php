<?php

namespace Tests\Feature;

use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_session_completed_activates_store(): void
    {
        Config::set('services.stripe.webhook_secret', 'whsec_test');
        Config::set('services.stripe.secret', '');

        $store = Store::query()->create([
            'code' => 'TEST',
            'name' => 'Tienda Test',
            'is_active' => true,
            'plan' => 'starter',
            'subscription_status' => 'inactive',
            'billing_method' => null,
            'trial_ends_at' => null,
            'subscription_ends_at' => null,
        ]);

        $payload = json_encode([
            'id' => 'evt_1',
            'object' => 'event',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_123',
                    'object' => 'checkout.session',
                    'client_reference_id' => (string) $store->id,
                    'customer' => 'cus_test_123',
                    'subscription' => 'sub_test_123',
                    'metadata' => [
                        'store_id' => (string) $store->id,
                        'plan' => 'pro',
                    ],
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $timestamp = time();
        $signedPayload = $timestamp.'.'.$payload;
        $signature = hash_hmac('sha256', $signedPayload, 'whsec_test');
        $header = 't='.$timestamp.',v1='.$signature;

        $res = $this->call(
            'POST',
            '/stripe/webhook',
            [],
            [],
            [],
            [
                'HTTP_Stripe-Signature' => $header,
                'CONTENT_TYPE' => 'application/json',
            ],
            $payload
        );

        $res->assertOk();

        $store->refresh();
        $this->assertSame('stripe', $store->billing_method);
        $this->assertSame('active', $store->subscription_status);
        $this->assertSame('pro', $store->plan);
        $this->assertSame('cus_test_123', $store->stripe_customer_id);
        $this->assertSame('sub_test_123', $store->stripe_subscription_id);
    }

    public function test_invoice_paid_creates_payment_row(): void
    {
        Config::set('services.stripe.webhook_secret', 'whsec_test');
        Config::set('services.stripe.secret', '');

        $store = Store::query()->create([
            'code' => 'TEST',
            'name' => 'Tienda Test',
            'is_active' => true,
            'plan' => 'starter',
            'subscription_status' => 'active',
            'billing_method' => 'stripe',
            'trial_ends_at' => null,
            'subscription_ends_at' => null,
            'stripe_customer_id' => 'cus_test_123',
            'stripe_subscription_id' => 'sub_test_123',
        ]);

        $payload = json_encode([
            'id' => 'evt_2',
            'object' => 'event',
            'type' => 'invoice.paid',
            'data' => [
                'object' => [
                    'id' => 'in_test_123',
                    'object' => 'invoice',
                    'customer' => 'cus_test_123',
                    'subscription' => 'sub_test_123',
                    'currency' => 'mxn',
                    'amount_paid' => 49900,
                    'period_start' => 1714000000,
                    'period_end' => 1716592000,
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $timestamp = time();
        $signedPayload = $timestamp.'.'.$payload;
        $signature = hash_hmac('sha256', $signedPayload, 'whsec_test');
        $header = 't='.$timestamp.',v1='.$signature;

        $res = $this->call(
            'POST',
            '/stripe/webhook',
            [],
            [],
            [],
            [
                'HTTP_Stripe-Signature' => $header,
                'CONTENT_TYPE' => 'application/json',
            ],
            $payload
        );

        $res->assertOk();

        $this->assertDatabaseHas('subscription_payments', [
            'store_id' => $store->id,
            'provider' => 'stripe',
            'reference_id' => 'in_test_123',
            'status' => 'paid',
            'currency' => 'MXN',
        ]);
    }
}
