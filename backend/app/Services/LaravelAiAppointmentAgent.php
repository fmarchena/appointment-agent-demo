<?php

namespace App\Services;

use App\Ai\Agents\AppointmentIntentAgent;

class LaravelAiAppointmentAgent
{
    public function extractPayload(string $message): array
    {
        $response = (new AppointmentIntentAgent)->prompt($message);

        return [
            'service' => $response['service'] ?? 'consulta_general',
            'date' => $response['date'] ?? null,
            'time' => $response['time'] ?? null,
            'people' => (int) ($response['people'] ?? 1),
        ];
    }
}
