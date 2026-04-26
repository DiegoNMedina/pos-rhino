<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionPayment extends Model
{
    protected $fillable = [
        'store_id',
        'provider',
        'event_type',
        'reference_id',
        'status',
        'currency',
        'amount',
        'period_start_at',
        'period_end_at',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'period_start_at' => 'datetime',
            'period_end_at' => 'datetime',
            'payload' => 'array',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
