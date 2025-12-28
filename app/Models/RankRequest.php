<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RankRequest extends Model
{
    public const STATUS_PENDING  = 0;
    public const STATUS_APPROVED = 1;
    public const STATUS_REJECTED = 2;

    protected $fillable = [
        'user_id',
        'status',
        'requested_at',

        'rank_id',
        'requested_rank_id',
        'requested_level',
        'note',

        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
    ];

    protected $casts = [
        'status' => 'integer',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rank(): BelongsTo
    {
        return $this->belongsTo(Rank::class, 'rank_id');
    }

    public function requestedRank(): BelongsTo
    {
        return $this->belongsTo(Rank::class, 'requested_rank_id');
    }

    // 担当者（承認/却下した管理者）
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING  => '未処理',
            self::STATUS_APPROVED => '承認',
            self::STATUS_REJECTED => '却下',
            default => (string)$this->status,
        };
    }

    // 履歴表示用：担当者名
    public function handledByName(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => $this->approver?->name ?? '-',
            self::STATUS_REJECTED => $this->rejector?->name ?? '-',
            default => '-',
        };
    }

    // 履歴表示用：日付（優先順位：承認/却下日時 → 申請日時）
    public function displayDateYyMmDd(): string
    {
        $dt = $this->approved_at ?? $this->rejected_at ?? $this->requested_at ?? $this->created_at;
        return $dt ? $dt->format('ymd') : '-';
    }
}
