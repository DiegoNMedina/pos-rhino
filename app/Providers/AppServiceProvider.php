<?php

namespace App\Providers;

use App\Enums\UserRole;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('manage-pos', function ($user) {
            return in_array($user->role, [UserRole::SUPER_ADMIN, UserRole::ADMIN, UserRole::SUPERVISOR], true);
        });

        Gate::define('manage-platform', function ($user) {
            return (string) $user->role === UserRole::SUPER_ADMIN;
        });

        Gate::define('manage-support', function ($user) {
            return in_array((string) $user->role, [UserRole::SUPER_ADMIN, UserRole::SUPPORT], true);
        });
    }
}
