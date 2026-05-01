# Proposal: Add AI Appointment Agent

## Summary

Add an AI chat agent that helps customers schedule appointments through a simple frontend.

The agent will extract appointment intent, validate the request through a guardrail engine, autocorrect safe violations, and create appointments only after the user confirms.

## Problem

Users may request appointments with invalid or incomplete data.

Examples:

- Outside business hours.
- Too many people.
- Missing date.
- Unsupported service.
- Blocked day.
- No available slot.

A normal validation flow blocks the user too early.

This demo introduces a guardrail flow where the system can guide the agent to correct safe issues before blocking.

## Goals

- Provide a customer chat interface.
- Provide an admin interface to configure appointment rules.
- Validate all agent actions before execution.
- Autocorrect safe issues using STEER decisions.
- Block unsafe or incomplete requests.
- Log all guardrail decisions.

## Non-Goals

- Build a production-grade calendar system.
- Integrate payments.
- Integrate external calendars.
- Allow the AI agent to bypass backend validation.

## Main Demo Scenario

User request:

"Quiero una cita mañana a las 8:00 p.m. para 3 personas"

Configured rules:

- Business hours end at 6:00 p.m.
- Maximum people per appointment is 2.

Expected behavior:

The guardrail returns STEER.

The agent offers a corrected appointment:

"Mañana a las 5:30 p.m. para 2 personas."
