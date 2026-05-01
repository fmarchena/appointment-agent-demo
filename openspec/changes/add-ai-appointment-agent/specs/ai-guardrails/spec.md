# Delta for AI Guardrails

## ADDED Requirements

### Requirement: Evaluate Agent Appointment Actions

The system SHALL evaluate all AI-generated appointment actions before calling the appointment API.

#### Scenario: Agent proposes appointment

- GIVEN the agent extracts appointment data from a user message
- WHEN the agent proposes an appointment payload
- THEN the guardrail engine SHALL evaluate the payload before execution

---

### Requirement: Allow Valid Appointment Payload

The guardrail engine SHALL allow valid appointment payloads.

#### Scenario: Appointment is valid

- GIVEN the appointment payload is inside business hours
- AND the requested people count is allowed
- AND the requested slot is available
- WHEN the guardrail evaluates the payload
- THEN the decision SHALL be ALLOW

---

### Requirement: Steer Appointment Outside Business Hours

The guardrail engine SHALL autocorrect appointment times outside business hours when a valid alternative exists.

#### Scenario: User requests appointment after business hours

- GIVEN business hours end at 18:00
- AND the user requests 20:00
- WHEN the guardrail evaluates the payload
- THEN the decision SHALL be STEER
- AND the corrected time SHALL be the nearest available slot before 18:00

---

### Requirement: Steer People Count Above Limit

The guardrail engine SHALL autocorrect people count when it exceeds the configured maximum.

#### Scenario: User requests too many people

- GIVEN max people is 2
- AND the user requests 3 people
- WHEN the guardrail evaluates the payload
- THEN the decision SHALL be STEER
- AND the corrected people count SHALL be 2

---

### Requirement: Block Missing Date

The guardrail engine SHALL block appointment creation when the date is missing.

#### Scenario: User does not provide date

- GIVEN the user requests an appointment without a date
- WHEN the guardrail evaluates the payload
- THEN the decision SHALL be BLOCK
- AND the agent SHALL ask the user for the missing date

---

### Requirement: Block Missing Service

The guardrail engine SHALL block appointment creation when the service is missing.

#### Scenario: User does not provide service

- GIVEN the user requests an appointment without a service
- WHEN the guardrail evaluates the payload
- THEN the decision SHALL be BLOCK
- AND the agent SHALL ask the user for the missing service

---

### Requirement: Steer Blocked Day

The guardrail engine SHALL suggest another date when the requested day is blocked.

#### Scenario: User requests appointment on a blocked day

- GIVEN the requested date is configured as blocked
- WHEN the guardrail evaluates the payload
- THEN the decision SHALL be STEER
- AND the corrected date SHALL be the nearest available business day

---

### Requirement: Log Guardrail Decisions

The system SHALL log all guardrail decisions.

#### Scenario: Guardrail returns STEER

- GIVEN the guardrail autocorrects a payload
- WHEN the decision is returned
- THEN the system SHALL store the original payload
- AND the corrected payload
- AND the decision
- AND the reason
