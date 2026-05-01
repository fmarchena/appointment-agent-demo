# Project: AI Appointment Booking Demo

## Purpose

Build a demo application where an AI chat agent helps users schedule appointments.

The system includes:

- Customer frontend
- Admin frontend
- Appointment API
- AI chat agent
- Guardrail engine

## Main Rule

The AI agent must not create appointments directly.

Every appointment action must pass through the guardrail engine before calling the appointment API.

## Guardrail Decisions

- ALLOW: the action is valid.
- STEER: the action can be safely corrected.
- BLOCK: the action cannot continue.
- ESCALATE: the action requires human review.

## Demo Scenario

User says:

"Quiero una cita mañana a las 8:00 p.m. para 3 personas"

Rules:

- Business hours end at 6:00 p.m.
- Maximum people per appointment is 2.

Expected behavior:

The system should offer the closest valid appointment slot and reduce the people count to the maximum allowed.