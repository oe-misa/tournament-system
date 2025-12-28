<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends Model
{
    protected $fillable = [
        'title',
        'description',
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
}
