<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_RECRUITING = 'recruiting';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_FINISHED = 'finished';

    protected $attributes = [
        'status' => self::STATUS_RECRUITING,
    ];

    protected $fillable = [
        'title',
        'description',
        'status',
        'event_date',
        'entry_deadline',
        'capacity',
        'min_rank_level',
    ];

    protected $casts = [
        'event_date' => 'date',
        'entry_deadline' => 'datetime',
    ];

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status ?? self::STATUS_RECRUITING) {
            self::STATUS_DRAFT => '下書き',
            self::STATUS_RECRUITING => '募集中',
            self::STATUS_CLOSED => '締切',
            self::STATUS_FINISHED => '終了',
            default => (string) ($this->status ?? self::STATUS_RECRUITING),
        };
    }

    public function isVisible(): bool
    {
        return ($this->status ?? self::STATUS_RECRUITING) !== self::STATUS_DRAFT;
    }
}
