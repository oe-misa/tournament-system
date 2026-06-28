<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entry extends Model
{
    public const STATUS_ENTRY = 'entry';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'tournament_id',
        'status',
    ];

    public function isEntry(): bool
    {
        return $this->status === self::STATUS_ENTRY;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }
}
