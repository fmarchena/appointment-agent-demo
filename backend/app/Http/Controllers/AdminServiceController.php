<?php

namespace App\Http\Controllers;

use App\Models\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminServiceController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => AppointmentService::query()
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:150'],
            'duration_minutes' => ['required', 'integer', 'min:5'],
            'active' => ['required', 'boolean'],
        ]);

        $service = AppointmentService::updateOrCreate(
            ['code' => $data['code']],
            [
                'name' => $data['name'],
                'duration_minutes' => $data['duration_minutes'],
                'active' => $data['active'],
            ]
        );

        return response()->json([
            'message' => 'Service saved successfully.',
            'data' => $service,
        ], 201);
    }

    public function update(Request $request, AppointmentService $service): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'duration_minutes' => ['required', 'integer', 'min:5'],
            'active' => ['required', 'boolean'],
        ]);

        $service->update($data);

        return response()->json([
            'message' => 'Service updated successfully.',
            'data' => $service,
        ]);
    }

    public function destroy(AppointmentService $service): JsonResponse
    {
        $service->delete();

        return response()->json([
            'message' => 'Service deleted successfully.',
        ]);
    }
}
