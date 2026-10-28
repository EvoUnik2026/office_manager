---
name: secure-development
description: Apply secure development practices when implementing or reviewing authentication, authorization, input handling, database access, messaging, configuration, logging, or other security-sensitive code in office_manager.
---

# Secure Development — office_manager

## Required reading

Read:
- `docs/engineering/standards.md`
- `docs/engineering/architecture.md`
- Relevant security-sensitive implementation and configuration
- `docs/engineering/testing-strategy.md` when tests are affected

Read the applicable persistence and migration guidelines when database changes
affect security or data integrity.

## Workflow

1. Identify the assets, users, trust boundaries, and security requirements
   affected by the change.
2. Inspect existing Symfony security configuration, access controls, input
   handling, persistence, and related tests.
3. Identify threats and misuse cases relevant to the actual feature.
4. Implement the smallest coherent change that addresses the requirement.
5. Add tests for relevant authorization boundaries, invalid input, and failure
   paths.
6. Review error responses, logs, configuration, and data exposure.
7. Run appropriate tests and quality checks.
8. Report remaining risks and checks that were not performed.

## Input validation and output handling

- Treat request parameters, message payloads, imported data, and external
  responses as untrusted.
- Validate data at the appropriate application boundary.
- Normalize input only where normalization matches the domain requirements.
- Use context-appropriate output escaping; do not rely on input validation alone
  to prevent output injection.
- Reject malformed or unsupported input explicitly.
- Avoid exposing internal exception details, stack traces, or implementation
  information to end users.

## Authentication and authorization

- Follow the existing Symfony Security architecture.
- Enforce authorization on the server side for protected operations.
- Do not treat hidden UI controls or client-side checks as authorization.
- Apply least privilege and deny access when authorization cannot be established.
- Test both permitted and forbidden access where applicable.
- Avoid changing access rules without understanding their effect on existing users
  and workflows.

## Database and persistence security

- Use Doctrine and parameterized database access according to project conventions.
- Do not construct unsafe SQL from untrusted input.
- Preserve domain invariants and database constraints.
- Verify the target environment before data-changing operations.
- Consider whether records or relationships expose information across access
  boundaries.
- Do not include secrets or unnecessary sensitive information in fixtures or tests.

## Secrets, configuration, and logging

- Never commit passwords, tokens, private keys, or production credentials.
- Use the project's established environment and secrets-management mechanisms.
- Do not log passwords, authentication tokens, or unnecessary personal data.
- Sanitize or structure untrusted log fields to reduce log injection risks.
- Keep error messages useful without disclosing confidential implementation
  details.
- Do not weaken TLS, authentication, or security settings merely to simplify
  local development without an explicit, reviewed reason.

## Messaging and asynchronous processing

Where MQTT, queues, webhooks, or other message inputs are involved:
- Validate message structure and values before processing.
- Apply the domain's rules for rejecting invalid or stale data.
- Avoid logging raw sensitive payloads unnecessarily.
- Consider duplicate delivery, replay, malformed messages, and processing failures.
- Do not assume a message source is trusted solely because it is internal.

## Dependency and configuration changes

- Prefer existing maintained dependencies when they meet the requirement.
- Review security and compatibility implications before adding or upgrading packages.
- Do not introduce new security tools or abstractions without a concrete benefit.
- Do not claim that a dependency or application is secure based only on a passing
  static-analysis or test run.

## Verification

Use relevant tests and the existing project checks:
- `docker compose exec php composer test`
- `docker compose exec php composer cs`
- `docker compose exec php composer stan`

Run targeted security tests where applicable. Report exactly which checks ran and
what they established; passing tests do not prove the absence of vulnerabilities.

## Completion criteria

Relevant trust boundaries and authorization rules are addressed, untrusted data
is handled safely, sensitive information is protected, applicable security tests
pass, and unresolved risks are documented.
