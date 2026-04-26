<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $fillable = [
        'code',
        'name',
        'is_active',
        'plan',
        'subscription_status',
        'billing_method',
        'trial_ends_at',
        'subscription_ends_at',
        'stripe_customer_id',
        'stripe_subscription_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'trial_ends_at' => 'datetime',
            'subscription_ends_at' => 'datetime',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function isSubscribed(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->subscription_status !== 'active' && $this->subscription_status !== 'trialing') {
            return false;
        }

        if ($this->billing_method === 'stripe') {
            return true;
        }

        if ($this->subscription_ends_at !== null) {
            $endsAt = $this->subscription_ends_at;
            $cutoff = $endsAt;

            if (
                $endsAt->isSameDay(now())
                && $endsAt->diffInMinutes($endsAt->copy()->startOfDay()) <= 60
            ) {
                $cutoff = $endsAt->copy()->endOfDay();
            }

            if ($cutoff->isPast()) {
                return false;
            }
        }

        return true;
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features(), true);
    }

    public function features(): array
    {
        switch ($this->plan) {
            case 'starter':
                return ['pos', 'products', 'tickets', 'basic_reports'];
            case 'pro':
                return ['pos', 'products', 'tickets', 'basic_reports', 'users', 'settings', 'scale', 'advanced_reports'];
            case 'enterprise':
                return ['pos', 'products', 'tickets', 'basic_reports', 'users', 'settings', 'scale', 'advanced_reports', 'integrations'];
            default:
                return ['pos', 'products', 'tickets', 'basic_reports'];
        }
    }
}
