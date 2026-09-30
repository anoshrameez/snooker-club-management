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
        'game_type',      // 'century', '6_ball', '10_ball', 'one_ball'
        'rate_applied',   // locked historical rate (per min for century, per round for ball games)
        'price_per_round',// legacy fallback
        'rounds',
        'total_price',
        'start_time',
        'end_time',
        'duration_seconds',
        'payment_status', // 'unpaid', 'paid'
        'payment_time',
        'payment_method', // 'cash' only
        'status',         // 'active', 'completed', 'cancelled'
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'rate_applied' => 'decimal:2',
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

    public function isTimeBased(): bool
    {
        return $this->game_type === 'century';
    }

    public function gameTitle(): string
    {
        return match ($this->game_type) {
            'century' => 'Century (Per Min)',
            '6_ball' => '6 Ball',
            '10_ball' => '10 Ball',
            'one_ball' => 'One Ball',
            default => '6 Ball',
        };
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

    public function elapsedMinutes(): int
    {
        $seconds = $this->elapsedSeconds();
        return max(1, (int) ceil($seconds / 60));
    }

    public function calculateTotal(): float
    {
        $rate = $this->rate_applied ?: $this->price_per_round;

        if ($this->isTimeBased()) {
            return (float) ($this->elapsedMinutes() * $rate);
        }

        return (float) ($this->rounds * $rate);
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
