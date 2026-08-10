<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Membership extends Model
{
    public const STATUS_PENDING_PAYMENT = 'pending_payment';
    public const STATUS_PAYMENT_CONFIRMED = 'payment_confirmed';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'note',
        'payment_reference',
        'status',
        'payment_confirmed_by',
        'payment_confirmed_at',
        'payment_confirmed_on',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'admin_comment',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'payment_confirmed_at' => 'datetime',
        'payment_confirmed_on' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentConfirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_confirmed_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
}
