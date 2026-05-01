<?php

namespace App\Http\Controllers;

use App\Models\AppointmentRule;
use App\Models\GuardrailLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminRuleController extends Controller
{
    public function show(): JsonResponse
    {
        $rules = AppointmentRule::query()->first();

        return response()->json([
            'data' => $rules,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'business_start_time' => ['required', 'date_format:H:i'],
            'business_end_time' => ['required', 'date_format:H:i'],
            'max_people' => ['required', 'integer', 'min:1'],
            'appointment_duration_minutes' => ['required', 'integer', 'min:5'],
            'allow_autocorrection' => ['required', 'boolean'],
        ]);

        $rules = AppointmentRule::query()->firstOrCreate(['id' => 1]);

        $rules->update([
            'business_start_time' => $data['business_start_time'] . ':00',
            'business_end_time' => $data['business_end_time'] . ':00',
            'max_people' => $data['max_people'],
            'appointment_duration_minutes' => $data['appointment_duration_minutes'],
            'allow_autocorrection' => $data['allow_autocorrection'],
        ]);

        return response()->json([
            'message' => 'Rules updated successfully.',
            'data' => $rules->fresh(),
        ]);
    }

    public function logs(): JsonResponse
    {
        $logs = GuardrailLog::query()
            ->latest()
            ->limit(20)
            ->get();

        return response()->json([
            'data' => $logs,
        ]);
    }
}
