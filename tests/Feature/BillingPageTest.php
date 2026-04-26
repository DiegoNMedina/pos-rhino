<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\SubscriptionPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class BillingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_open_billing_page_and_see_payments(): void
    {
        $store = Store::query()->create([
            'code' => 'TEST',
            'name' => 'Tienda Test',
            'is_active' => true,
            'plan' => 'pro',
            'subscription_status' => 'active',
            'billing_method' => 'stripe',
            'trial_ends_at' => null,
            'subscription_ends_at' => now()->addDays(30),
            'stripe_customer_id' => 'cus_test_123',
            'stripe_subscription_id' => 'sub_test_123',
        ]);

        $user = User::factory()->create([
            'store_id' => $store->id,
        ]);

        SubscriptionPayment::query()->create([
            'store_id' => $store->id,
            'provider' => 'stripe',
            'event_type' => 'invoice.paid',
            'reference_id' => 'in_test_123',
            'status' => 'paid',
            'currency' => 'MXN',
            'amount' => 499.00,
            'period_start_at' => now()->subDays(1),
            'period_end_at' => now()->addDays(29),
            'payload' => ['ok' => true],
        ]);

        $res = $this->actingAs($user)->get('/billing');
        $res->assertOk();
        $res->assertSee('Facturación');
        $res->assertSee('in_test_123');
    }

    public function test_portal_shows_error_when_stripe_not_configured(): void
    {
        Config::set('services.stripe.secret', '');

        $store = Store::query()->create([
            'code' => 'TEST',
            'name' => 'Tienda Test',
            'is_active' => true,
            'plan' => 'pro',
            'subscription_status' => 'active',
            'billing_method' => 'stripe',
            'trial_ends_at' => null,
            'subscription_ends_at' => now()->addDays(30),
            'stripe_customer_id' => 'cus_test_123',
            'stripe_subscription_id' => 'sub_test_123',
        ]);

        $user = User::factory()->create([
            'store_id' => $store->id,
        ]);

        $res = $this->actingAs($user)->from('/billing')->post('/billing/portal');
        $res->assertRedirect('/billing');
        $res->assertSessionHas('error');
    }
}
