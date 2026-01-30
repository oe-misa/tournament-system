<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OmikujiDraw extends Model
{
    protected $fillable = [
        'user_id',
        'result',
        'drawn_on',
    ];

    protected $casts = [
        'drawn_on' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
