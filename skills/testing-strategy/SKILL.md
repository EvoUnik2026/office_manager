---
name: testing-strategy
description: Plan, write, debug, and review PHPUnit tests in office_manager. Use when implementing features, fixing bugs, changing business rules, modifying persistence, or reviewing test coverage.
---

# Testing Strategy — office_manager

## Required reading

Read:
- `docs/engineering/standards.md`
- `docs/engineering/testing-strategy.md`

Also consult `docs/engineering/architecture.md` for domain or architectural
changes, and `docs/engineering/database-migrations.md` for persistence changes.

Inspect the existing PHPUnit configuration and nearby tests before changing
test organization or configuration.

## Workflow

1. Identify the behaviour and acceptance criteria that need verification.
2. Inspect the relevant production code and existing tests.
3. Choose the appropriate test level: unit, integration, or functional.
4. Define meaningful success, rejection, boundary, and failure scenarios.
5. Write focused tests alongside the implementation change.
6. Run the narrowest relevant test first.
7. Diagnose failures before changing production code or expectations.
8. Run broader suites and quality checks according to the change's risk.
9. Review the diff for missing coverage, weakened assertions, and unrelated edits.

## Unit tests

- Test isolated business rules, value objects, validation, and state transitions.
- Prefer real domain objects for domain behaviour.
- Use test doubles only to isolate genuine collaborators or simulate relevant failures.
- Cover valid and invalid transitions, boundaries, and important invariants.
- Assert observable outcomes rather than incidental implementation details.

## Integration tests

Use integration tests when correctness depends on component interaction or
infrastructure, including Doctrine persistence, repository queries, database
constraints, transactions, or message processing.

- Use the configured test environment.
- Verify database configuration before destructive operations.
- Keep test data isolated and tests independent of execution order.
- Verify persistence and retrieval, not merely that a method was called.
- Test constraints and failure behaviour where relevant.

## Functional tests

Where HTTP workflows exist, test relevant requests, responses, authorization,
validation, and user-visible outcomes.

Do not add a new test framework or suite without a concrete requirement.

## Regression and failure handling

- Add a regression test for a confirmed defect when practical.
- Do not delete, skip, weaken, or loosen valid tests merely to obtain a passing run.
- Do not catch or suppress test failures to make a suite appear green.
- If requirements intentionally change, update affected tests and explain the change.
- When a test fails, determine whether the cause is the implementation, test,
  environment, or an unresolved requirement.

## Project commands

Run commands from the PHP container, using the application working directory
configured by Docker Compose:

- `docker compose exec php composer test:unit`
- `docker compose exec php composer test:integration`
- `docker compose exec php composer test`
- `docker compose exec php composer cs`
- `docker compose exec php composer stan`

Run the specific failing test first when appropriate. Do not claim a check passed
unless it actually completed successfully.

## Completion criteria

Relevant behaviour is covered by appropriate tests, regressions are addressed,
the required checks have been run, and remaining failures or limitations are
reported honestly.
