<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentRule extends Model
{
    protected $fillable = [
        'business_start_time',
        'business_end_time',
        'max_people',
        'appointment_duration_minutes',
        'allow_autocorrection',
    ];

    protected $casts = [
        'allow_autocorrection' => 'boolean',
    ];
}
