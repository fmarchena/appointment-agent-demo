# Tasks

## 1. Project Setup

- [ ] 1.1 Create backend API project.
- [ ] 1.2 Create customer frontend.
- [ ] 1.3 Create admin frontend.
- [ ] 1.4 Configure environment variables.

## 2. Database

- [x] 2.1 Create appointments table.
- [x] 2.2 Create appointment_rules table.
- [x] 2.3 Create blocked_days table.
- [x] 2.4 Create services table.
- [x] 2.5 Create guardrail_logs table.

## 3. Admin Rules

- [ ] 3.1 Create endpoint to get rules.
- [ ] 3.2 Create endpoint to update rules.
- [ ] 3.3 Create admin UI form for business hours.
- [ ] 3.4 Create admin UI form for max people.
- [ ] 3.5 Create admin UI for blocked days.
- [ ] 3.6 Create admin UI for services.

## 4. Appointment API

- [x] 4.1 Create endpoint to check availability.
- [ ] 4.2 Create endpoint to create appointment.
- [ ] 4.3 Prevent double booking.
- [ ] 4.4 Validate appointment status.

## 5. Guardrail Engine

- [x] 5.1 Implement ALLOW decision.
- [x] 5.2 Implement STEER decision for invalid time.
- [x] 5.3 Implement STEER decision for max people.
- [x] 5.4 Implement BLOCK decision for missing date.
- [x] 5.5 Implement BLOCK decision for missing service.
- [x] 5.6 Implement STEER decision for blocked day.
- [x] 5.7 Log all guardrail decisions.

## 6. AI Chat Agent

- [x] 6.1 Create chat endpoint.
- [x] 6.2 Extract appointment intent.
- [x] 6.3 Build proposed appointment payload.
- [x] 6.4 Call guardrail engine.
- [x] 6.5 Ask user for confirmation before creating appointment.
- [ ] 6.6 Create appointment after confirmation.

## 7. Customer Frontend

- [ ] 7.1 Create chat screen.
- [ ] 7.2 Show agent messages.
- [ ] 7.3 Show corrected appointment proposal.
- [ ] 7.4 Add confirm button.
- [ ] 7.5 Show final confirmation.

## 8. Admin Frontend

- [ ] 8.1 Create admin dashboard.
- [ ] 8.2 Create rules configuration screen.
- [ ] 8.3 Create services management screen.
- [ ] 8.4 Create blocked days screen.
- [ ] 8.5 Show guardrail logs.

## 9. Testing

- [ ] 9.1 Test valid appointment.
- [ ] 9.2 Test appointment outside business hours.
- [ ] 9.3 Test too many people.
- [ ] 9.4 Test missing date.
- [ ] 9.5 Test blocked day.
- [ ] 9.6 Test no available slot.
