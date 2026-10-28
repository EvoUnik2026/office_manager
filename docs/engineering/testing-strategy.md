# Testing Strategy — office_manager

## 1. Purpose

This document defines how tests should protect business behaviour, persistence,
and application workflows. It complements `standards.md` and must remain
consistent with it.

Tests are executable specifications of intended behaviour. They should detect
regressions and make failures understandable.

## 2. Existing test setup

The project currently uses:
- PHPUnit 13.
- Docker Compose for the application runtime.
- A `unit` test suite.
- An `integration` test suite.
- Composer scripts for running the configured suites.

Inspect `app/phpunit.dist.xml`, `app/composer.json`, and the existing tests
before changing test configuration or introducing additional tooling.

Do not assume that a test suite covers a category of tests merely because of
its name; inspect what its tests actually verify.

## 3. Choose the appropriate test level

### Unit tests

Use unit tests to verify isolated business rules, value objects, domain state
transitions, validation logic, and other behaviour that can be tested without
booting the full application or depending on a real database.

- Keep tests focused on observable behaviour.
- Cover valid transitions, invalid transitions, and relevant boundary cases.
- Use test doubles only for collaborators whose behaviour is outside the
  specific unit under test.
- Avoid mocking the class or rule whose behaviour the test is intended to prove.

### Integration tests

Use integration tests when correctness depends on collaboration between
components or infrastructure.

Examples include:
- Doctrine persistence and retrieval.
- Repository queries.
- Database constraints and transactions.
- Application services interacting with persistence.
- Message-processing or other infrastructure integration where applicable.

Use the project's configured test environment. Keep test data isolated and
avoid depending on execution order or pre-existing database state.

### Functional tests

Use functional tests for HTTP requests, controller behaviour, authorization,
forms, and complete user-facing workflows when those behaviours exist.

Follow the project's existing organization. Do not create a new test framework
or suite solely to match this document; propose configuration changes when
there is a concrete need.

## 4. What tests should cover

For each behaviour change, consider:
- The expected successful outcome.
- Invalid input and rejected operations.
- Boundary conditions and state transitions.
- Relevant missing records and duplicate requests.
- Authorization and access boundaries, where applicable.
- Persistence effects and related entity state.
- Failure handling, transaction boundaries, and retry behaviour where relevant.
- Regression cases for previously confirmed defects.

Not every test needs every category. Choose cases according to the actual
risks and acceptance criteria.

## 5. Test quality

- Assert meaningful results and externally observable state.
- Keep each test focused on a clear behaviour.
- Use descriptive names that explain the scenario and expected outcome.
- Make tests deterministic and independent of execution order.
- Avoid unnecessary sleeps, timing assumptions, and external network dependencies.
- Do not hide failures with broad exception handling.
- Do not skip, delete, weaken, or loosen valid tests just to obtain a green run.
- Do not duplicate extensive production logic inside tests.
- Use data providers when they make related cases clearer.
- Add regression tests for confirmed defects before or alongside the fix.

## 6. Database and integration test safety

- Know which database and environment each test uses.
- Never run destructive tests against development or production data.
- Use explicit test configuration and verify it before database-changing work.
- Clean up test data reliably or use an isolation strategy appropriate to the test.
- When adding tables or foreign keys, update shared integration-test cleanup and
  delete dependent rows before referenced rows unless the setup has a deliberate,
  reliably restored foreign-key strategy.
- Test important database constraints rather than relying exclusively on
  application-level validation.
- Review the effects of migrations on test setup and existing data.
- Do not change production schema or migration history merely to make a test pass.

## 7. Test doubles and boundaries

- Prefer real domain objects for domain-rule tests.
- Use mocks or stubs when they isolate a genuine external dependency or allow
  a specific failure scenario to be tested.
- Avoid asserting incidental call sequences unless call order is part of the
  contract.
- Use integration tests to verify important wiring and persistence behaviour
  that unit tests cannot establish.

## 8. Running tests and diagnosing failures

Use the existing Composer scripts from the application container:

- `composer test:unit`
- `composer test:integration`
- `composer test`

Run the narrowest relevant test first, then broaden verification according to
the scope and risk of the change.

When a test fails:
1. Read the complete failure and identify the violated expectation.
2. Determine whether the implementation, test, environment, or requirement is
   responsible.
3. Inspect related tests and production code.
4. Fix the underlying cause without weakening valid assertions.
5. Rerun the failing test, then the relevant suite.

Do not report tests as passing unless the commands actually completed
successfully.

## 9. Quality checks

For changes involving PHP code, use the project's configured checks as
appropriate:

- `composer cs`
- `composer stan`
- Relevant PHPUnit tests.
- `composer test` for full unit and integration verification when warranted.

Review the final diff for accidental changes, missing regression coverage,
unintended test deletions, and generated test artifacts.

## 10. Future improvements

Consider mutation testing, coverage reporting, or additional automated checks
only when they address a demonstrated gap. Evaluate their maintenance cost and
value before adding dependencies or changing CI.

A passing test suite is evidence, not proof, of correctness. Combine automated
tests with code review, domain-rule verification, and appropriate operational
checks.
