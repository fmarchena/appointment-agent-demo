# AI Appointment Booking Demo

A demo application that showcases how an **AI chat agent** with a **guardrail engine** can safely help users schedule appointments — without the AI ever booking directly.

## Overview

The user describes what they want in natural language. A Grok (xAI) powered agent extracts the intent, and a guardrail engine validates and auto-corrects the request before any appointment is created.

**Demo scenario:**
> "Quiero una cita mañana a las 8pm para 3 personas"

Given business hours end at 18:00 and max 2 people per appointment, the system steers the request to the closest valid slot with the allowed capacity.

## Architecture

```
User Input (natural language)
        │
        ▼
  Grok Agent (intent extraction)
        │
        ▼
  Guardrail Engine (validate + auto-correct)
        │
   ┌────┴─────────────────┐
ALLOW / STEER         BLOCK / ESCALATE
        │
        ▼
  Appointment API (confirm)
```

### Guardrail Decisions

| Decision | Meaning |
|---|---|
| `ALLOW` | Action is valid, proceed |
| `STEER` | Action corrected automatically |
| `BLOCK` | Action cannot continue |
| `ESCALATE` | Requires human review |

## Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 11 (PHP 8.2) |
| Frontend | Vue 3 + Vite |
| Database | MySQL 8.0 |
| AI Model | Grok (`grok-3-mini`) via xAI API |
| Containers | Docker + Docker Compose |

## Project Structure

```
appointment-agent-demo/
├── backend/            # Laravel API
│   ├── app/Services/
│   │   ├── GrokAppointmentAgent.php   # Intent extraction via Grok
│   │   └── GuardrailEngine.php        # Rule evaluation engine
│   └── routes/api.php
├── frontend/           # Vue 3 single-page app
│   └── src/App.vue         # Customer chat + Admin panel
├── docker/
│   ├── php/Dockerfile
│   └── frontend/Dockerfile
└── docker-compose.yml
```

## Prerequisites

- Docker + Docker Compose
- An [xAI API key](https://console.x.ai/)

## Getting Started

### 1. Clone and configure

```bash
git clone <repo-url>
cd appointment-agent-demo
```

Create `backend/.env` (copy from `backend/.env.example` if available) and set your xAI key:

```env
XAI_API_KEY=xai-your-key-here
XAI_BASE_URL=https://api.x.ai/v1
XAI_MODEL=grok-3-mini

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=appointment_agent
DB_USERNAME=appointment_user
DB_PASSWORD=appointment_pass
```

### 2. Start containers

```bash
docker-compose up --build
```

| Service | URL |
|---|---|
| Frontend | http://localhost:5173 |
| Backend API | http://localhost:8000/api |
| MySQL | localhost:3307 |

### 3. Run migrations and seeders

```bash
docker exec appointment-agent-backend php artisan migrate --seed
```

## API Reference

### Chat (main entry point)

```http
POST /api/chat
Content-Type: application/json

{ "message": "Quiero una cita mañana a las 8pm para 3 personas" }
```

Response includes the extracted payload, guardrail decision, corrections applied, and the proposal ready to confirm.

### Confirm appointment

```http
POST /api/appointments/confirm
Content-Type: application/json

{
  "customer_name": "Francisco",
  "customer_contact": "francisco@test.com",
  "service": "consulta_general",
  "date": "2026-05-02",
  "time": "17:30:00",
  "people": 2
}
```

### Admin endpoints

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/admin/rules` | Get business rules |
| `PUT` | `/api/admin/rules` | Update business rules |
| `GET` | `/api/admin/guardrail-logs` | View all guardrail decisions |
| `GET/POST/DELETE` | `/api/admin/blocked-days` | Manage blocked days |
| `GET/POST/PUT/DELETE` | `/api/admin/services` | Manage available services |

## Guardrail Engine Rules

The engine evaluates the following in order:

1. **Service exists and is active** — blocks if not
2. **Date is not blocked** — steers to next available day
3. **Time is within business hours** — steers to `business_end - duration` if after close
4. **People ≤ max allowed** — steers down to max if exceeded
5. **No overlapping appointments** — blocks if slot is taken

Business rules (configurable via admin panel):

| Rule | Default |
|---|---|
| Business hours | 08:00 – 18:00 |
| Max people | 2 |
| Appointment duration | 30 min |
| Allow auto-correction | true |

## Frontend

The Vue 3 frontend includes two views toggled by a tab:

**Customer view**
- Free-text chat input pre-filled with the demo scenario
- Shows extracted intent, guardrail result, corrections applied
- Confirm button to finalize the appointment

**Admin view**
- Edit business rules
- Manage blocked days
- Manage active services
- View guardrail decision log with full payloads

## Available Services (seeded)

| Code | Name |
|---|---|
| `consulta_general` | Consulta General |
| `limpieza_facial` | Limpieza Facial |
| `asesoria` | Asesoría |

## Running Tests

```bash
docker exec appointment-agent-backend php artisan test
```

## License

MIT
