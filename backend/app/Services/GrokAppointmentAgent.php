<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class GrokAppointmentAgent
{
    public function extractPayload(string $message): array
    {
        $today = Carbon::now()->toDateString();
        $tomorrow = Carbon::tomorrow()->toDateString();

        $system = <<<PROMPT
You are an appointment intent extraction agent.

Return ONLY valid JSON. No markdown. No explanation.

Extract this schema:
{
  "service": "consulta_general|limpieza_facial|asesoria|null",
  "date": "YYYY-MM-DD|null",
  "time": "HH:MM|null",
  "people": number
}

Rules:
- Today is {$today}.
- Tomorrow is {$tomorrow}.
- If the user says "mañana", use {$tomorrow}.
- If the user says "hoy", use {$today}.
- If the user mentions "limpieza facial", use "limpieza_facial".
- If the user mentions "asesoría" or "asesoria", use "asesoria".
- If service is not clear, use "consulta_general".
- If people is not clear, use 1.
- Convert 8pm to 20:00.
- Convert 10am to 10:00.
PROMPT;

        $response = Http::withToken(config('services.xai.key'))
            ->timeout(30)
            ->post(rtrim(config('services.xai.url'), '/') . '/chat/completions', [
                'model' => config('services.xai.model'),
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => $message],
                ],
                'temperature' => 0,
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Grok API error: ' . $response->body());
        }

        $content = $response->json('choices.0.message.content');

        $payload = json_decode($content, true);

        if (!is_array($payload)) {
            throw new \RuntimeException('Grok returned invalid JSON: ' . $content);
        }

        return array_filter([
            'service' => $payload['service'] ?? null,
            'date' => $payload['date'] ?? null,
            'time' => $payload['time'] ?? null,
            'people' => $payload['people'] ?? 1,
        ], fn ($value) => $value !== null && $value !== '');
    }
}
