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
        'status',
        'current_session_id',
    ];

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
}
