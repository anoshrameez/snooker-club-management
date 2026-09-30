<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClubTable extends Model
{
    use HasFactory;

    protected $table = 'tables';

    protected $fillable = [
        'name',
        'type',
        'rate_century',
        'rate_6ball',
        'rate_10ball',
        'rate_oneball',
        'status',
        'current_session_id',
    ];

    protected function casts(): array
    {
        return [
            'rate_century' => 'decimal:2',
            'rate_6ball' => 'decimal:2',
            'rate_10ball' => 'decimal:2',
            'rate_oneball' => 'decimal:2',
        ];
    }

    public function gameSessions(): HasMany
    {
        return $this->hasMany(GameSession::class, 'table_id');
    }

    public function currentSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class, 'current_session_id');
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available' && empty($this->current_session_id);
    }

    public function isOccupied(): bool
    {
        return $this->status === 'occupied' || !empty($this->current_session_id);
    }

    public function isMaintenance(): bool
    {
        return $this->status === 'maintenance';
    }

    /**
     * Get the specific table price for any of the 4 supported gameplays.
     */
    public function getRateForGame(string $gameType): float
    {
        return match ($gameType) {
            'century' => (float) ($this->rate_century ?: Setting::get('default_rate_century', 10)),
            '6_ball' => (float) ($this->rate_6ball ?: Setting::get('default_rate_6ball', 130)),
            '10_ball' => (float) ($this->rate_10ball ?: Setting::get('default_rate_10ball', 150)),
            'one_ball' => (float) ($this->rate_oneball ?: Setting::get('default_rate_oneball', 120)),
            default => (float) ($this->rate_6ball ?: 130),
        };
    }
}
