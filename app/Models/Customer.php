<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'notes',
    ];

    public function gameSessions(): HasMany
    {
        return $this->hasMany(GameSession::class, 'customer_id')->latest();
    }

    public function totalSessionsCount(): int
    {
        return $this->gameSessions()->where('status', '!=', 'cancelled')->count();
    }

    public function totalRoundsCount(): int
    {
        return (int) $this->gameSessions()->where('status', '!=', 'cancelled')->sum('rounds');
    }

    public function totalSpent(): float
    {
        return (float) $this->gameSessions()->where('status', '!=', 'cancelled')->sum('total_price');
    }

    public function totalPaid(): float
    {
        return (float) $this->gameSessions()->where('payment_status', 'paid')->where('status', '!=', 'cancelled')->sum('total_price');
    }

    public function totalUnpaid(): float
    {
        return (float) $this->gameSessions()->where('payment_status', 'unpaid')->where('status', '!=', 'cancelled')->sum('total_price');
    }
}
