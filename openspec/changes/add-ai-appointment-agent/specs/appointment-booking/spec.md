# Delta for Appointment Booking

## ADDED Requirements

### Requirement: Create Appointment After User Confirmation

The system SHALL create an appointment only after the user confirms the proposed appointment details.

#### Scenario: User confirms appointment

- GIVEN the agent proposes an appointment
- AND the user confirms the appointment
- WHEN the system receives the confirmation
- THEN the system SHALL create the appointment
- AND the system SHALL return a confirmation message

---

### Requirement: Prevent Appointment Creation Without Availability

The system SHALL not create appointments for unavailable time slots.

#### Scenario: Requested slot is unavailable

- GIVEN a requested time slot is already booked
- WHEN the user attempts to schedule an appointment
- THEN the system SHALL reject that slot
- AND the agent SHALL offer the nearest available slot

---

### Requirement: Store Appointment Details

The system SHALL store confirmed appointment details.

#### Scenario: Appointment is created

- GIVEN the user confirms a valid appointment
- WHEN the appointment is created
- THEN the system SHALL store the service, date, time, people, and status
