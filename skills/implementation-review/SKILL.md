---
name: implementation-review
description: Review proposed or completed changes in office_manager for correctness, regressions, architecture, testing, persistence safety, security, and adherence to engineering standards. Use before completing significant changes or reporting work as done.
---

# Implementation Review — office_manager

## Required reading

Read:
- `docs/engineering/standards.md`
- `docs/engineering/definition-of-done.md`
- Relevant architecture, testing, migration, and security guidelines
- The applicable skills for the change under review

Inspect the current Git status and diff before reviewing changes.

## Review workflow

1. Confirm the requested outcome and acceptance criteria.
2. Inspect the changed files and relevant surrounding implementation.
3. Compare the implementation with the requirements and documented domain rules.
4. Trace important execution paths, dependencies, and affected related entities.
5. Inspect tests and identify missing success, rejection, boundary, or failure cases.
6. Review persistence, migrations, and data integrity where applicable.
7. Review security and authorization implications where applicable.
8. Inspect the complete relevant diff for accidental or unrelated changes.
9. Run appropriate tests and quality checks when authorized and practical.
10. Report findings clearly, including severity, evidence, and recommended action.

## Review priorities

### Correctness and domain behaviour

- Verify that acceptance criteria are met.
- Check invalid state transitions and domain invariants.
- Check related entities and parent-child relationships affected by a change.
- Identify assumptions or unresolved domain decisions.
- Look for race conditions, duplicate processing, partial failure, and retry
  problems where relevant.

### Architecture and maintainability

- Check that responsibilities and dependency directions remain clear.
- Identify duplicated business rules and unnecessary abstractions.
- Check that controllers, commands, repositories, and application services
  have appropriate responsibilities.
- Flag unrelated refactoring that increases review risk.

### Tests and verification

- Check that tests verify observable behaviour and important invariants.
- Identify missing regression tests and meaningful failure cases.
- Flag tests that were removed, skipped, weakened, or made less meaningful
  without an approved reason.
- Distinguish checks that were executed from checks that are merely recommended.
- Do not report a passing result without actual successful command output.

### Persistence and security

When applicable:
- Verify mapping, migration, schema, and relationship consistency.
- Check for unintended data loss or unsafe destructive operations.
- Review authorization, validation, sensitive logging, and error disclosure.
- Check for committed secrets or sensitive data.

## Findings format

For each finding, provide:
- Severity: Critical, High, Medium, or Low.
- File and relevant line or code location, when available.
- The concrete problem and its likely impact.
- A recommended corrective action.

Prioritize actionable defects over stylistic preferences. Do not label a
preference as a defect unless it conflicts with a documented requirement or
creates a meaningful maintenance risk.

If no findings are identified, state the scope reviewed and list any important
checks that were not performed. Do not imply that the code is defect-free.

## Safe review behaviour

- Do not modify code merely because a review is requested.
- Do not discard, reset, or stage unrelated work.
- Ask before performing destructive or irreversible operations.
- Separate confirmed defects from possible risks and questions requiring
  domain clarification.
- Do not expand the review into unrelated refactoring.

## Completion criteria

The review identifies actionable issues and verification gaps, respects the
project's engineering standards, preserves existing work, and provides an
accurate account of the review scope and results.
