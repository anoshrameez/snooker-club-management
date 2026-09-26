<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_code',
        'customer_id',
        'table_id',
        'user_id',
        'price_per_round',
        'rounds',
        'total_price',
        'start_time',
        'end_time',
        'duration_seconds',
        'payment_status',
        'payment_time',
        'payment_method',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'price_per_round' => 'decimal:2',
            'total_price' => 'decimal:2',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'payment_time' => 'datetime',
            'rounds' => 'integer',
            'duration_seconds' => 'integer',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(ClubTable::class, 'table_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'game_session_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isUnpaid(): bool
    {
        return $this->payment_status === 'unpaid';
    }

    public function calculateTotal(): float
    {
        return (float) ($this->rounds * $this->price_per_round);
    }

    public function elapsedSeconds(): int
    {
        if ($this->end_time && $this->duration_seconds > 0) {
            return $this->duration_seconds;
        }

        if (!$this->start_time) {
            return 0;
        }

        return max(0, Carbon::now()->diffInSeconds($this->start_time));
    }

    public function formattedDuration(): string
    {
        $seconds = $this->elapsedSeconds();
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes} min";
    }

    public function timerClockString(): string
    {
        $seconds = $this->elapsedSeconds();
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }
}
