# Delta for Admin Rules

## ADDED Requirements

### Requirement: Configure Business Hours

The admin SHALL be able to configure business start and end time.

#### Scenario: Admin updates business hours

- GIVEN the admin is on the rules screen
- WHEN the admin updates the business hours
- THEN the system SHALL save the new hours
- AND guardrail validation SHALL use the updated hours

---

### Requirement: Configure Maximum People

The admin SHALL be able to configure the maximum number of people per appointment.

#### Scenario: Admin updates max people

- GIVEN the admin is on the rules screen
- WHEN the admin changes max people
- THEN the system SHALL save the new maximum
- AND future guardrail validations SHALL use the updated maximum

---

### Requirement: Configure Blocked Days

The admin SHALL be able to configure blocked days.

#### Scenario: Admin adds blocked day

- GIVEN the admin is on the blocked days screen
- WHEN the admin adds a blocked day
- THEN the system SHALL save the blocked day
- AND guardrail validation SHALL prevent appointments on that day

---

### Requirement: View Guardrail Logs

The admin SHALL be able to view guardrail decision logs.

#### Scenario: Admin views guardrail logs

- GIVEN guardrail decisions have been logged
- WHEN the admin opens the guardrail logs screen
- THEN the system SHALL display the decision, reason, original payload, and corrected payload
