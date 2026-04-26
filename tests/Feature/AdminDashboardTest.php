<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_admin_pages(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@pos.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.products.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.sales.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.reports.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk();
    }

    public function test_cashier_cannot_open_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => UserRole::CASHIER]);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
