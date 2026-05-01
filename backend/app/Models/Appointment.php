<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_contact',
        'service',
        'appointment_date',
        'appointment_time',
        'people',
        'status',
    ];
}
