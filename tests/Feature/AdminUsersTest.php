<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_user(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@pos.test')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Nuevo Usuario',
                'email' => 'nuevo@pos.test',
                'role' => UserRole::CASHIER,
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'nuevo@pos.test',
            'role' => UserRole::CASHIER,
        ]);
    }

    public function test_admin_can_update_user_without_changing_password(): void
    {
        $this->seed();

        $admin = User::query()->where('email', 'admin@pos.test')->firstOrFail();
        $store = Store::query()->whereKey($admin->store_id)->firstOrFail();
        $target = User::factory()->create([
            'email' => 'target@pos.test',
            'role' => UserRole::CASHIER,
            'store_id' => $store->id,
        ]);
        $originalPassword = $target->password;

        $this->actingAs($admin)
            ->put(route('admin.users.update', $target), [
                'name' => 'Target Updated',
                'email' => 'target@pos.test',
                'role' => UserRole::SUPERVISOR,
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect(route('admin.users.index'));

        $target->refresh();

        $this->assertSame(UserRole::SUPERVISOR, $target->role);
        $this->assertSame($originalPassword, $target->password);
    }
}
