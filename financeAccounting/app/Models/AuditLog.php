<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'loggable_id',
        'loggable_type',
        'action',
        'description',
        'old_values',
        'new_values',
        'user',
    ];

    public function loggable()
    {
        return $this->morphTo();
    }
}
