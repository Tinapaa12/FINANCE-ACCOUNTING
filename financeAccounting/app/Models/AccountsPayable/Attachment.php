<?php

namespace App\Models\AccountsPayable;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'attachable_id',
        'attachable_type',
        'filename',
        'original_filename',
        'mime_type',
        'size',
    ];

    public function attachable()
    {
        return $this->morphTo();
    }
}
