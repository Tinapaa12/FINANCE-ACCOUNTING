<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'loggable_id',
        'loggable_type',
        'action',
        'description',
        'old_values',
        'new_values',
        'user',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function loggable()
    {
        return $this->morphTo();
    }
}
