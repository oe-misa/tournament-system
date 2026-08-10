<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipNotification extends Model
{
    public const TYPE_EXPIRING_30_DAYS = 'expiring_30_days';
    public const TYPE_EXPIRING_7_DAYS = 'expiring_7_days';
    public const TYPE_EXPIRED = 'expired';

    protected $fillable = ['user_id', 'type', 'fiscal_year'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
