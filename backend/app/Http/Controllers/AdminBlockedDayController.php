<?php

namespace App\Http\Controllers;

use App\Models\BlockedDay;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminBlockedDayController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => BlockedDay::query()
                ->orderBy('date')
                ->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'reason' => ['nullable', 'string'],
        ]);

        $blockedDay = BlockedDay::updateOrCreate(
            ['date' => $data['date']],
            ['reason' => $data['reason'] ?? null]
        );

        return response()->json([
            'message' => 'Blocked day saved successfully.',
            'data' => $blockedDay,
        ], 201);
    }

    public function destroy(BlockedDay $blockedDay): JsonResponse
    {
        $blockedDay->delete();

        return response()->json([
            'message' => 'Blocked day deleted successfully.',
        ]);
    }
}
