# Engineering Standards — office_manager

## 1. Purpose and authority

This document is the authoritative source for engineering standards in
office_manager. Supporting engineering documents, agent instructions, and
reusable skills must remain consistent with it.

When a requirement is unclear, inspect the existing implementation and tests
before proposing a change. Do not invent domain rules or silently change
previously agreed behaviour.

## 2. Current technology baseline

- PHP: current project runtime is PHP 8.5.
- Symfony: 7.4 LTS.
- Database: MariaDB 11.4.
- Testing: PHPUnit 13.
- Formatting: PHP-CS-Fixer.
- Static analysis: PHPStan, level 8.
- Runtime and verification: Docker Compose and the existing Composer scripts.

The Composer PHP requirement currently permits PHP >=8.2. Do not introduce
language features that violate the declared minimum version. If the minimum
version needs to change, propose and verify that change explicitly.

Do not add a dependency, framework, abstraction, or tool without a concrete
requirement and a clear benefit.

## 3. Mandatory code-quality rules

- Follow the existing PHP-CS-Fixer configuration and keep `composer cs` passing.
- Use strict types in PHP files where applicable.
- Keep `composer stan` passing at the configured PHPStan level.
- Use clear, intention-revealing names.
- Prefer small, cohesive methods and classes with explicit responsibilities.
- Validate external input at application boundaries.
- Make invalid states difficult to represent and reject invalid transitions.
- Handle errors explicitly; do not silently swallow failures.
- Avoid duplicated business rules and unnecessary abstractions.
- Do not introduce dead code, unused dependencies, or unexplained suppressions.
- Add comments to explain intent or non-obvious constraints, not to repeat code.

## 4. Architecture and domain rules

- Preserve the existing project architecture unless a change is justified.
- Keep controllers focused on HTTP concerns and application coordination.
- Put business decisions in the appropriate domain or application layer.
- Keep infrastructure concerns, such as database and messaging access, out of
  domain logic where practical.
- Use dependency injection rather than constructing infrastructure services
  throughout the application.
- Enforce domain invariants in a reliable place; do not rely on UI validation
  alone.
- Treat domain rules as explicit requirements, not assumptions inferred from
  class names or database structure.
- Avoid broad architectural refactoring as part of an unrelated feature.
- Document significant architectural decisions and their trade-offs.

## 5. Testing standards

- Use unit tests for isolated business rules and value-object behaviour.
- Use integration tests for persistence, repositories, and interactions between
  application components and infrastructure.
- Use functional tests for user-facing or HTTP workflows when applicable.
- Test successful behaviour, invalid input, boundary conditions, and relevant
  failure paths.
- Add regression tests for confirmed defects.
- Keep tests deterministic, isolated, and independent of execution order.
- Assert meaningful outcomes and state changes, not implementation details
  without a good reason.
- Do not weaken, delete, skip, or loosen a valid test merely to make a change
  pass.
- Do not mock the behaviour that a test is intended to verify.
- Update tests when requirements intentionally change, and explain why.

## 6. Database and persistence

- Treat migrations as versioned production changes.
- Review generated migrations before applying them.
- Preserve existing data unless data removal is an explicit, approved
  requirement.
- Consider foreign keys, unique constraints, nullability, indexes, and
  transaction boundaries.
- Check consistency between entity mappings, migrations, and the actual schema.
- Do not use schema updates that bypass the project's migration process in
  shared or production environments.
- Verify migration and rollback implications before recommending deployment.

## 7. Security

- Follow Symfony security conventions and least-privilege principles.
- Enforce authorization on the server side.
- Validate and normalize untrusted input appropriately for its context.
- Use parameterized database access and safe output rendering.
- Never commit secrets, credentials, tokens, or real sensitive data.
- Avoid logging passwords, tokens, raw sensitive payloads, or unnecessary
  personal data.
- Handle errors without exposing internal details to end users.
- Add security tests for authorization boundaries and security-sensitive flows.

## 8. Implementation workflow

Before implementation:
1. Restate the requirement and acceptance criteria.
2. Inspect relevant code, tests, mappings, configuration, and domain rules.
3. Identify affected components, risks, and dependencies.
4. Propose the smallest coherent implementation plan.
5. Ask for clarification when a missing domain decision materially affects
   correctness.

During implementation:
1. Make focused, incremental changes.
2. Preserve unrelated and uncommitted work.
3. Follow existing naming and architectural conventions unless there is a
   justified reason to change them.
4. Add or update tests alongside the behaviour being changed.
5. Avoid unrelated formatting, refactoring, and dependency updates.

Before completion:
1. Run the relevant tests.
2. Run `composer cs`.
3. Run `composer stan`.
4. Run `composer test` when the change warrants the full configured test suites.
5. Review the diff for regressions, accidental changes, secrets, and generated
   files.
6. Report commands run, results, known limitations, and any checks not run.

## 9. Definition of done

A change is complete when:
- The agreed acceptance criteria are met.
- Relevant tests cover the new or changed behaviour.
- Existing behaviour and domain invariants are preserved unless a change was
  explicitly intended.
- Formatting and static analysis pass.
- Relevant test suites pass, or remaining failures are clearly reported.
- Database and security implications have been reviewed where applicable.
- Documentation is updated when behaviour, architecture, setup, or operations
  change.
- The diff contains no unrelated changes or unexplained suppressions.

## 10. Engineering judgement

These standards are mandatory unless explicitly marked otherwise. Apply
recommendations proportionately to the change.

Prefer simple, maintainable solutions over speculative generalization. Do not
introduce design patterns, interfaces, extra layers, or additional tools solely
to satisfy an abstract notion of clean code.

If a standard conflicts with an existing constraint, identify the conflict and
propose a resolution instead of silently bypassing the rule.
