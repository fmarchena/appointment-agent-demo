<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AppointmentRule;
use App\Models\AppointmentService;
use App\Models\BlockedDay;
use App\Models\GuardrailLog;
use Carbon\Carbon;

class GuardrailEngine
{
    public function evaluate(array $payload): array
    {
        $originalPayload = $payload;

        $rules = AppointmentRule::query()->first();

        if (!$rules) {
            return $this->block($originalPayload, null, 'Appointment rules are not configured.');
        }

        if (empty($payload['service'])) {
            return $this->block($originalPayload, null, 'Service is required.');
        }

        $serviceExists = AppointmentService::where('code', $payload['service'])
            ->where('active', true)
            ->exists();

        if (!$serviceExists) {
            return $this->block($originalPayload, null, 'Service is not available.');
        }

        if (empty($payload['date'])) {
            return $this->block($originalPayload, null, 'Date is required.');
        }

        if (empty($payload['time'])) {
            return $this->block($originalPayload, null, 'Time is required.');
        }

        $corrections = [];

        $date = Carbon::parse($payload['date'])->toDateString();
        $time = Carbon::parse($payload['time'])->format('H:i:s');

        $payload['date'] = $date;
        $payload['time'] = $time;

        $blockedDay = BlockedDay::where('date', $date)->first();

        if ($blockedDay) {
            $newDate = Carbon::parse($date)->addDay();

            while (BlockedDay::where('date', $newDate->toDateString())->exists()) {
                $newDate->addDay();
            }

            $corrections[] = [
                'field' => 'date',
                'from' => $payload['date'],
                'to' => $newDate->toDateString(),
                'reason' => 'Requested date is blocked.',
            ];

            $payload['date'] = $newDate->toDateString();
        }

        $businessStart = Carbon::parse($rules->business_start_time)->format('H:i:s');
        $businessEnd = Carbon::parse($rules->business_end_time)->format('H:i:s');

        if ($time < $businessStart) {
            $corrections[] = [
                'field' => 'time',
                'from' => $payload['time'],
                'to' => $businessStart,
                'reason' => 'Requested time is before business hours.',
            ];

            $payload['time'] = $businessStart;
        }

        if ($time > $businessEnd) {
            $duration = (int) $rules->appointment_duration_minutes;
            $correctedTime = Carbon::parse($businessEnd)->subMinutes($duration)->format('H:i:s');

            $corrections[] = [
                'field' => 'time',
                'from' => $payload['time'],
                'to' => $correctedTime,
                'reason' => 'Requested time is after business hours.',
            ];

            $payload['time'] = $correctedTime;
        }

        $people = (int) ($payload['people'] ?? 1);

        if ($people > $rules->max_people) {
            $corrections[] = [
                'field' => 'people',
                'from' => $people,
                'to' => (int) $rules->max_people,
                'reason' => 'Requested people count exceeds maximum allowed.',
            ];

            $payload['people'] = (int) $rules->max_people;
        } else {
            $payload['people'] = $people;
        }

        $slotTaken = Appointment::where('appointment_date', $payload['date'])
            ->where('appointment_time', $payload['time'])
            ->where('service', $payload['service'])
            ->exists();

       if ($slotTaken) {
    $newTime = Carbon::parse($payload['time'])->addMinutes((int) $rules->appointment_duration_minutes);
    $businessEndTime = Carbon::parse($rules->business_end_time);

    while (
        $newTime->lessThan($businessEndTime) &&
        Appointment::where('appointment_date', $payload['date'])
            ->where('appointment_time', $newTime->format('H:i:s'))
            ->where('service', $payload['service'])
            ->exists()
    ) {
        $newTime->addMinutes((int) $rules->appointment_duration_minutes);
    }

    if ($newTime->lessThan($businessEndTime)) {
        $corrections[] = [
            'field' => 'time',
            'from' => $payload['time'],
            'to' => $newTime->format('H:i:s'),
            'reason' => 'Requested slot is already booked. Offering next available slot.',
        ];

        $payload['time'] = $newTime->format('H:i:s');

        return $this->steer($originalPayload, $payload, $corrections);
    }

    return $this->block($originalPayload, $payload, 'Requested slot is already booked and no later slot is available.');
}

        if (!empty($corrections)) {
            return $this->steer($originalPayload, $payload, $corrections);
        }

        return $this->allow($originalPayload, $payload);
    }

    private function allow(array $originalPayload, array $payload): array
    {
        GuardrailLog::create([
            'original_payload' => $originalPayload,
            'corrected_payload' => $payload,
            'decision' => 'ALLOW',
            'reason' => 'Payload is valid.',
        ]);

        return [
            'decision' => 'ALLOW',
            'reason' => 'Payload is valid.',
            'payload' => $payload,
        ];
    }

    private function steer(array $originalPayload, array $payload, array $corrections): array
    {
        $rules = AppointmentRule::query()->first();

        if ($rules && !$rules->allow_autocorrection) {
            return $this->block(
                $originalPayload,
                $payload,
                'Payload requires correction, but autocorrection is disabled.'
            );
        }

        GuardrailLog::create([
            'original_payload' => $originalPayload,
            'corrected_payload' => $payload,
            'decision' => 'STEER',
            'reason' => 'Payload was corrected.',
        ]);

        return [
            'decision' => 'STEER',
            'reason' => 'Payload was corrected.',
            'corrections' => $corrections,
            'corrected_payload' => $payload,
        ];
    }

    private function block(array $originalPayload, ?array $correctedPayload, string $reason): array
    {
        GuardrailLog::create([
            'original_payload' => $originalPayload,
            'corrected_payload' => $correctedPayload,
            'decision' => 'BLOCK',
            'reason' => $reason,
        ]);

        return [
            'decision' => 'BLOCK',
            'reason' => $reason,
        ];
    }
}
