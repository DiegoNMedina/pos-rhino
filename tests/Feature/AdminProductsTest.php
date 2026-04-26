<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_products_index(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@pos.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.products.index'))
            ->assertOk();
    }

    public function test_cashier_cannot_open_products_index(): void
    {
        $user = User::factory()->create(['role' => UserRole::CASHIER]);

        $this->actingAs($user)
            ->get(route('admin.products.index'))
            ->assertForbidden();
    }
}
