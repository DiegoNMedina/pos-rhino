<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCustomersTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_customers_index(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@pos.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.customers.index'))
            ->assertOk()
            ->assertSee('Clientes');
    }

    public function test_admin_can_create_customer(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@pos.test')->firstOrFail();

        $res = $this->actingAs($admin)->post(route('admin.customers.store'), [
            'name' => 'Juan Pérez',
            'phone' => '5512345678',
            'email' => 'juan@example.com',
            'address' => 'Calle 1',
            'tax_id' => 'JUAP800101XX0',
            'notes' => 'Cliente frecuente',
        ]);

        $res->assertRedirect(route('admin.customers.index'));

        $this->assertDatabaseHas('customers', [
            'store_id' => $admin->store_id,
            'name' => 'Juan Pérez',
            'phone' => '5512345678',
            'email' => 'juan@example.com',
            'tax_id' => 'JUAP800101XX0',
        ]);
    }

    public function test_customer_is_scoped_to_store(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@pos.test')->firstOrFail();

        $otherStore = Store::query()->create([
            'code' => 'OTRA',
            'name' => 'Otra tienda',
            'is_active' => true,
            'plan' => 'starter',
            'subscription_status' => 'active',
            'billing_method' => 'transfer',
            'trial_ends_at' => null,
            'subscription_ends_at' => now()->addDays(30),
        ]);

        $other = User::factory()->create([
            'store_id' => $otherStore->id,
            'role' => 'admin',
        ]);

        $customer = Customer::query()->create([
            'store_id' => $admin->store_id,
            'name' => 'Cliente tienda',
        ]);

        $this->actingAs($other)
            ->get(route('admin.customers.edit', $customer))
            ->assertNotFound();
    }
}
