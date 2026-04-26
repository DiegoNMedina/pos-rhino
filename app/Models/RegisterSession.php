<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegisterSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'branch_id',
        'register_id',
        'opened_by_user_id',
        'opened_at',
        'opening_cash',
        'closed_by_user_id',
        'closed_at',
        'closing_cash',
        'expected_cash',
        'difference',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
            'opening_cash' => 'decimal:2',
            'closing_cash' => 'decimal:2',
            'expected_cash' => 'decimal:2',
            'difference' => 'decimal:2',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function register(): BelongsTo
    {
        return $this->belongsTo(Register::class);
    }

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by_user_id');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }
}
