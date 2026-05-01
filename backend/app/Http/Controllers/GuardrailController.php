<?php

namespace App\Http\Controllers;

use App\Services\GuardrailEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuardrailController extends Controller
{
    public function evaluate(Request $request, GuardrailEngine $guardrailEngine): JsonResponse
    {
        $payload = $request->validate([
            'service' => ['nullable', 'string'],
            'date' => ['nullable', 'date'],
            'time' => ['nullable', 'string'],
            'people' => ['nullable', 'integer', 'min:1'],
        ]);

        $result = $guardrailEngine->evaluate($payload);

        return response()->json($result);
    }
}
