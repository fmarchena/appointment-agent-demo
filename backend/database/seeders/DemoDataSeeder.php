<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppointmentRule;
use App\Models\AppointmentService;
use App\Models\BlockedDay;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        AppointmentRule::updateOrCreate(
            ['id' => 1],
            [
                'business_start_time' => '08:00:00',
                'business_end_time' => '18:00:00',
                'max_people' => 2,
                'appointment_duration_minutes' => 30,
                'allow_autocorrection' => true,
            ]
        );

        AppointmentService::updateOrCreate(
            ['code' => 'consulta_general'],
            [
                'name' => 'Consulta General',
                'duration_minutes' => 30,
                'active' => true,
            ]
        );

        AppointmentService::updateOrCreate(
            ['code' => 'limpieza_facial'],
            [
                'name' => 'Limpieza Facial',
                'duration_minutes' => 45,
                'active' => true,
            ]
        );

        BlockedDay::updateOrCreate(
            ['date' => now()->addDays(3)->toDateString()],
            [
                'reason' => 'Día bloqueado para demo',
            ]
        );
    }
}
