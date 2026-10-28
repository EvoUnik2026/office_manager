---
name: clean-code
description: Apply maintainable PHP coding practices in office_manager. Use when writing, modifying, refactoring, or reviewing PHP code.
---

# Clean Code — office_manager

## Required reading

Read `docs/engineering/standards.md` before applying this skill.
Consult `docs/engineering/architecture.md` when a change affects responsibilities,
dependencies, or architectural boundaries.

## Workflow

1. Inspect the relevant classes, their callers, tests, and nearby conventions.
2. Identify the intended behaviour and acceptance criteria before changing code.
3. Make the smallest coherent change that satisfies the requirement.
4. Keep each class and method focused on a clear responsibility.
5. Use intention-revealing names and explicit types where appropriate.
6. Prefer straightforward control flow over clever or unnecessarily abstract code.
7. Validate untrusted input at application boundaries and handle failures explicitly.
8. Avoid duplicated business rules, dead code, unnecessary dependencies, and
   speculative abstractions.
9. Add comments only when they explain intent, constraints, or non-obvious decisions.
10. Add or update tests for changed behaviour.
11. Review the diff for unrelated changes and unintended behaviour.

## PHP and Symfony conventions

- Follow the project's existing PHP-CS-Fixer configuration.
- Use strict types in PHP files where applicable.
- Respect the PHP version declared in `app/composer.json`; do not rely only on
  the version installed in the container.
- Follow existing Symfony and dependency-injection conventions.
- Keep controllers focused on HTTP concerns and application coordination.
- Put business decisions in the appropriate domain or application layer.
- Do not introduce interfaces, traits, services, or extra layers without a
  concrete benefit.
- Do not suppress static-analysis findings without understanding and documenting
  the reason.

## Verification

Run the relevant tests, then use these checks as appropriate:

- `docker compose exec php composer cs`
- `docker compose exec php composer stan`
- `docker compose exec php composer test`

Review the output. Fix issues caused by the change rather than weakening valid
quality checks. Report any checks that were not run.

## Completion criteria

The code is understandable, focused, consistent with the project standards,
covered by appropriate tests, and free of unrelated changes.
