<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuardrailLog extends Model
{
    protected $fillable = [
        'original_payload',
        'corrected_payload',
        'decision',
        'reason',
    ];

    protected $casts = [
        'original_payload' => 'array',
        'corrected_payload' => 'array',
    ];
}
