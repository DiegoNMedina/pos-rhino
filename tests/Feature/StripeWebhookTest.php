<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\SubscriptionPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\DataProvider;
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

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('services.stripe.webhook_secret', 'whsec_test');
        Config::set('services.stripe.secret', '');
    }

    private function createBillingStore(string $code): Store
    {
        return Store::query()->create([
            'code' => $code,
            'name' => $code,
            'is_active' => true,
            'plan' => 'starter',
            'subscription_status' => 'inactive',
            'stripe_customer_id' => 'cus_'.$code,
            'stripe_subscription_id' => 'sub_'.$code,
        ]);
    }

    private function sendWebhook(string $payload, ?string $signature = null)
    {
        $timestamp = time();
        $signature ??= 't='.$timestamp.',v1='.hash_hmac('sha256', $timestamp.'.'.$payload, 'whsec_test');

        return $this->call('POST', '/stripe/webhook', [], [], [], [
            'HTTP_Stripe-Signature' => $signature,
            'CONTENT_TYPE' => 'application/json',
        ], $payload);
    }

    private function invoicePayload(string $type, array $identifiers, bool $withLines = false): string
    {
        $invoice = array_merge([
            'id' => 'in_regression',
            'object' => 'invoice',
            'currency' => 'mxn',
            'amount_paid' => $type === 'invoice.paid' ? 49900 : 0,
            'period_start' => 1714000000,
            'period_end' => 1716592000,
        ], $identifiers);
        if ($withLines) {
            $invoice['lines']['data'] = [
                ['period' => ['start' => 1714000100, 'end' => 1716592100]],
                ['period' => ['start' => 1714000200, 'end' => 1716592200]],
            ];
        }

        return json_encode([
            'id' => 'evt_regression',
            'object' => 'event',
            'type' => $type,
            'data' => ['object' => $invoice],
        ], JSON_THROW_ON_ERROR);
    }

    public static function unmatchedInvoices(): iterable
    {
        foreach (['invoice.paid', 'invoice.payment_failed'] as $type) {
            foreach ([
                'absent' => [],
                'null' => ['customer' => null, 'subscription' => null],
                'empty' => ['customer' => '', 'subscription' => ''],
                'whitespace' => ['customer' => ' ', 'subscription' => ' '],
                'unknown_customer' => ['customer' => 'cus_unknown'],
                'unknown_subscription' => ['subscription' => 'sub_unknown'],
            ] as $case => $ids) {
                yield $type.' '.$case => [$type, $ids];
            }
        }
    }

    #[DataProvider('unmatchedInvoices')]
    public function test_unmatched_invoice_does_not_change_any_store(string $type, array $ids): void
    {
        $first = $this->createBillingStore('first');
        $second = $this->createBillingStore('second');
        // Empty identifiers must not match even legacy rows containing empty strings.
        $first->update(['stripe_customer_id' => '', 'stripe_subscription_id' => '']);
        $before = Store::query()->orderBy('id')->get()->toArray();

        $this->sendWebhook($this->invoicePayload($type, $ids))->assertOk();

        $this->assertSame($before, Store::query()->orderBy('id')->get()->toArray());
        $this->assertDatabaseCount('subscription_payments', 0);
    }

    public static function matchedInvoices(): iterable
    {
        foreach (['invoice.paid', 'invoice.payment_failed'] as $type) {
            yield $type.' customer' => [$type, ['customer' => 'cus_second', 'subscription' => 'sub_second'], true];
            yield $type.' subscription' => [$type, ['subscription' => 'sub_second'], false];
            yield $type.' empty customer' => [$type, ['customer' => '', 'subscription' => 'sub_second'], false];
        }
    }

    #[DataProvider('matchedInvoices')]
    public function test_invoice_updates_only_matching_store(string $type, array $ids, bool $withLines): void
    {
        $first = $this->createBillingStore('first');
        $second = $this->createBillingStore('second');
        $before = $first->fresh()->toArray();
        $payload = $this->invoicePayload($type, $ids, $withLines);

        $this->sendWebhook($payload)->assertOk();
        $this->sendWebhook($payload)->assertOk();

        $this->assertSame($before, $first->fresh()->toArray());
        $this->assertSame($type === 'invoice.paid' ? 'active' : 'past_due', $second->fresh()->subscription_status);
        $this->assertSame('cus_second', $second->fresh()->stripe_customer_id);
        $this->assertSame('stripe', $second->fresh()->billing_method);
        $this->assertDatabaseCount('subscription_payments', 1);
        $payment = SubscriptionPayment::query()->sole();
        $this->assertSame($second->id, $payment->store_id);
        $this->assertSame($type === 'invoice.paid' ? 'paid' : 'failed', $payment->status);
        $this->assertSame($type, $payment->event_type);
        $this->assertSame('MXN', $payment->currency);
        $this->assertSame($type === 'invoice.paid' ? '499.00' : '0.00', $payment->amount);
        $this->assertSame($withLines ? 1714000100 : 1714000000, $payment->period_start_at->timestamp);
        $this->assertSame($withLines ? 1716592200 : 1716592000, $payment->period_end_at->timestamp);
        if ($type === 'invoice.paid') {
            $this->assertSame($payment->period_end_at->timestamp, $second->fresh()->subscription_ends_at->timestamp);
        } else {
            $this->assertNull($second->fresh()->subscription_ends_at);
        }
    }

    public function test_invalid_signatures_and_signed_invalid_json_are_rejected(): void
    {
        $this->createBillingStore('first');
        $this->createBillingStore('second');
        $before = Store::all()->toArray();
        $payload = $this->invoicePayload('invoice.paid', ['customer' => 'cus_first']);

        $this->sendWebhook($payload, '')->assertStatus(400);
        $this->sendWebhook($payload, 't='.time().',v1=invalid')->assertStatus(400);
        $this->sendWebhook('{invalid json')->assertStatus(400);

        $this->assertSame($before, Store::all()->toArray());
        $this->assertDatabaseCount('subscription_payments', 0);
    }

    public function test_subscription_events_still_update_the_matching_store(): void
    {
        $first = $this->createBillingStore('first');
        $second = $this->createBillingStore('second');
        $before = $first->fresh()->toArray();
        foreach (['customer.subscription.updated' => 'active', 'customer.subscription.deleted' => 'canceled'] as $type => $status) {
            $payload = json_encode([
                'id' => 'evt_subscription',
                'object' => 'event',
                'type' => $type,
                'data' => ['object' => [
                    'id' => 'sub_second',
                    'customer' => 'cus_second',
                    'status' => 'active',
                    'metadata' => ['store_id' => (string) $second->id, 'plan' => 'pro'],
                    'current_period_end' => 1716592000,
                ]],
            ], JSON_THROW_ON_ERROR);
            $this->sendWebhook($payload)->assertOk();
            $this->assertSame($status, $second->fresh()->subscription_status);
            $this->assertSame(1716592000, $second->fresh()->subscription_ends_at->timestamp);
            $this->assertSame('pro', $second->fresh()->plan);
            $this->assertSame($before, $first->fresh()->toArray());
        }
    }
}
