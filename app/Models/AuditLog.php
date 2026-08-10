<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = ['actor_id', 'event', 'auditable_type', 'auditable_id', 'changed_fields'];
    protected $casts = ['changed_fields' => 'array'];
    public function actor(): BelongsTo { return $this->belongsTo(User::class, 'actor_id'); }
}
