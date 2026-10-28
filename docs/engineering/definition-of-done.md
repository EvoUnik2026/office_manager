# Definition of Done — office_manager

A feature or bug fix is complete only when the applicable items below have
been verified. Mark items that do not apply as not applicable rather than
performing unnecessary work.

## 1. Requirements

- [ ] The requested behaviour and acceptance criteria are clear.
- [ ] Relevant domain rules and existing behaviour have been inspected.
- [ ] Ambiguous decisions affecting correctness or data integrity are resolved.
- [ ] Unrelated behaviour has not been changed unintentionally.

## 2. Implementation

- [ ] Responsibilities are clear and code follows the project's architecture.
- [ ] Business rules are enforced outside the UI where appropriate.
- [ ] Input validation and error handling are explicit.
- [ ] No unnecessary abstractions, dependencies, dead code, or suppressions were added.
- [ ] Security and authorization implications have been considered.

## 3. Tests

- [ ] Relevant successful scenarios are tested.
- [ ] Invalid inputs, boundaries, and failure paths are covered as appropriate.
- [ ] Regression tests have been added for confirmed defects.
- [ ] Tests are deterministic and do not depend on execution order.
- [ ] Tests have not been weakened or removed merely to obtain a passing result.
- [ ] Relevant PHPUnit tests pass.

## 4. Persistence and data

When applicable:
- [ ] Entity mappings, migrations, and schema changes are consistent.
- [ ] Relationships, constraints, and deletion or archival behaviour are correct.
- [ ] Existing data is preserved or transformed according to approved requirements.
- [ ] Migration and recovery risks have been reviewed.
- [ ] Persistence tests use the intended test database.

## 5. Quality checks

- [ ] `composer cs` passes.
- [ ] `composer stan` passes.
- [ ] Relevant tests pass; `composer test` passes when full suite verification is warranted.
- [ ] Relevant security and authorization checks have been completed.

## 6. Review and documentation

- [ ] The final diff has been reviewed for accidental or unrelated changes.
- [ ] No secrets, credentials, or unintended generated files are included.
- [ ] Documentation is updated when behaviour, architecture, setup, or operations change.
- [ ] Known limitations and checks not performed are documented.

## 7. Completion report

Report:
- What changed and why.
- Tests and quality checks actually executed, including results.
- Migration or operational considerations, if any.
- Any remaining risks, limitations, or follow-up work.

Do not claim completion or successful verification for checks that were not run.
