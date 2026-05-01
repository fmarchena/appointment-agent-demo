<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentService extends Model
{
    protected $table = 'services';

    protected $fillable = [
        'code',
        'name',
        'duration_minutes',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
