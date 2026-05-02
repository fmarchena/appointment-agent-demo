<?php

namespace App\Ai\Agents;

use Carbon\Carbon;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;
use Stringable;

#[Provider(Lab::xAI)]
#[Model('grok-3-mini')]
#[Temperature(0)]
#[Timeout(30)]
class AppointmentIntentAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        $today = Carbon::now()->toDateString();
        $tomorrow = Carbon::tomorrow()->toDateString();

        return <<<PROMPT
You are an appointment intent extraction agent.

Your only job is to convert a user's natural language appointment request into a structured payload.

Return structured output only.

Today is {$today}.
Tomorrow is {$tomorrow}.

Rules:
- If the user says "mañana", use {$tomorrow}.
- If the user says "hoy", use {$today}.
- If the user mentions "limpieza facial", use "limpieza_facial".
- If the user mentions "asesoría" or "asesoria", use "asesoria".
- If service is not clear, use "consulta_general".
- If people is not clear, use 1.
- Convert 8pm to 20:00.
- Convert 10am to 10:00.
- Use date format YYYY-MM-DD.
- Use time format HH:MM.
- Do not decide if the appointment is valid.
- Do not correct business rules.
- The GuardrailEngine will validate and correct the payload.
PROMPT;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'service' => $schema->string()
                ->enum(['consulta_general', 'limpieza_facial', 'asesoria'])
                ->required(),

            'date' => $schema->string()->required(),

            'time' => $schema->string()->required(),

            'people' => $schema->integer()->required(),
        ];
    }
}
