<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Services\GuardrailEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function confirm(Request $request, GuardrailEngine $guardrailEngine): JsonResponse
    {
        $payload = $request->validate([
            'customer_name' => ['nullable', 'string'],
            'customer_contact' => ['nullable', 'string'],
            'service' => ['required', 'string'],
            'date' => ['required', 'date'],
            'time' => ['required', 'string'],
            'people' => ['required', 'integer', 'min:1'],
        ]);

        $guardrailResult = $guardrailEngine->evaluate($payload);

        if ($guardrailResult['decision'] !== 'ALLOW') {
            return response()->json([
                'status' => 'not_confirmed',
                'message' => 'The appointment cannot be confirmed without accepting the corrected proposal.',
                'guardrail' => $guardrailResult,
            ], 422);
        }

        $validPayload = $guardrailResult['payload'];

        $appointment = Appointment::create([
            'customer_name' => $payload['customer_name'] ?? null,
            'customer_contact' => $payload['customer_contact'] ?? null,
            'service' => $validPayload['service'],
            'appointment_date' => $validPayload['date'],
            'appointment_time' => $validPayload['time'],
            'people' => $validPayload['people'],
            'status' => 'confirmed',
        ]);

        return response()->json([
            'status' => 'confirmed',
            'appointment_id' => $appointment->id,
            'message' => "Your appointment was confirmed for {$appointment->appointment_date} at {$appointment->appointment_time}.",
            'appointment' => $appointment,
        ], 201);
    }
}
