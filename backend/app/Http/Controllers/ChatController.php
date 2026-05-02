<?php

namespace App\Http\Controllers;

use App\Services\GuardrailEngine;
use App\Services\LaravelAiAppointmentAgent;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function message(Request $request, GuardrailEngine $guardrailEngine, LaravelAiAppointmentAgent $agent): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string'],
        ]);

        $message = $data['message'];

        $payload = $agent->extractPayload($message);

        $guardrailResult = $guardrailEngine->evaluate($payload);

        if ($guardrailResult['decision'] === 'ALLOW') {
            return response()->json([
                'type' => 'appointment_proposal',
                'message' => 'Tengo una cita disponible. ¿Deseas confirmarla?',
                'proposal' => $guardrailResult['payload'],
                'requires_confirmation' => true,
                'guardrail' => $guardrailResult,
            ]);
        }

        if ($guardrailResult['decision'] === 'STEER') {
            return response()->json([
                'type' => 'appointment_proposal',
                'message' => $this->buildCorrectionMessage($guardrailResult),
                'proposal' => $guardrailResult['corrected_payload'],
                'requires_confirmation' => true,
                'guardrail' => $guardrailResult,
            ]);
        }

        return response()->json([
            'type' => 'missing_information',
            'message' => $this->buildBlockMessage($guardrailResult),
            'requires_confirmation' => false,
            'guardrail' => $guardrailResult,
        ], 422);
    }

    private function extractAppointmentPayload(string $message): array
    {
        $payload = [
            'service' => 'consulta_general',
            'date' => Carbon::tomorrow()->toDateString(),
            'time' => '10:00',
            'people' => 1,
        ];

        if (str_contains($message, 'limpieza facial')) {
            $payload['service'] = 'limpieza_facial';
        }

        if (str_contains($message, 'hoy')) {
            $payload['date'] = Carbon::today()->toDateString();
        }

        if (str_contains($message, 'mañana')) {
            $payload['date'] = Carbon::tomorrow()->toDateString();
        }

        if (str_contains($message, 'domingo')) {
            $payload['date'] = Carbon::now()->next('Sunday')->toDateString();
        }

        if (str_contains($message, '8pm') || str_contains($message, '8:00 p.m') || str_contains($message, '8:00 pm')) {
            $payload['time'] = '20:00';
        }

        if (str_contains($message, '7pm') || str_contains($message, '7:00 p.m') || str_contains($message, '7:00 pm')) {
            $payload['time'] = '19:00';
        }

        if (str_contains($message, '10am') || str_contains($message, '10:00 a.m') || str_contains($message, '10:00 am')) {
            $payload['time'] = '10:00';
        }

        if (preg_match('/(\d+)\s*(personas|persona)/', $message, $matches)) {
            $payload['people'] = (int) $matches[1];
        }

        if (str_contains($message, 'sin fecha')) {
            unset($payload['date']);
        }

        if (str_contains($message, 'sin servicio')) {
            unset($payload['service']);
        }

        return $payload;
    }

    private function buildCorrectionMessage(array $guardrailResult): string
    {
        $parts = [];

        foreach ($guardrailResult['corrections'] ?? [] as $correction) {
            if ($correction['field'] === 'time') {
                $parts[] = "La hora solicitada no cumple las reglas. Te puedo ofrecer {$correction['to']}.";
            }

            if ($correction['field'] === 'people') {
                $parts[] = "El máximo permitido es {$correction['to']} persona(s).";
            }

            if ($correction['field'] === 'date') {
                $parts[] = "La fecha solicitada está bloqueada. Te puedo ofrecer {$correction['to']}.";
            }
        }

        $parts[] = '¿Deseas confirmar esta propuesta?';

        return implode(' ', $parts);
    }

    private function buildBlockMessage(array $guardrailResult): string
    {
        return match ($guardrailResult['reason']) {
            'Date is required.' => 'Necesito que me indiques la fecha para revisar disponibilidad.',
            'Service is required.' => 'Necesito que me indiques el servicio que deseas agendar.',
            'Service is not available.' => 'Ese servicio no está disponible. Por favor elige un servicio activo.',
            'Payload requires correction, but autocorrection is disabled.' => 'La solicitud necesita corrección, pero la autocorrección está desactivada por el admin.',
            'Time is required.' => 'Necesito que me indiques la hora de la cita.',
            default => $guardrailResult['reason'],
        };
    }
}
