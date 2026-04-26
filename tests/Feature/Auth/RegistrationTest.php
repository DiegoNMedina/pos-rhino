<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        Config::set('services.stripe.secret', '');
        Config::set('services.stripe.prices.pro', '');

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'plan' => 'pro',
            'billing_method' => 'stripe',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('pricing', ['plan' => 'pro']));
    }
}
