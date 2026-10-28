---
name: testing
description: >
  Use when designing, writing, debugging, or reviewing PHPUnit tests in
  office_manager. Follow docs/engineering/testing-strategy.md as the source
  of truth for test quality and suite selection.
---

# Testing

## Required reading

Read `docs/engineering/testing-strategy.md` and
`docs/engineering/standards.md`. Consult architecture and migration guidance
when those behaviours are involved.

## Workflow

1. Inspect the configured suites, shared test setup, and nearby tests before
   choosing where a test belongs.
2. Test observable behaviour at the narrowest appropriate boundary.
3. Keep tests deterministic and isolated; use the configured test database
   only for tests that require persistence or infrastructure.
4. When adding tables or relationships, update cleanup without deleting
   unrelated test data, and respect foreign-key ordering.
5. Add regression coverage for confirmed defects and preserve valid
   assertions.
6. Run focused tests first, then broader suites as warranted. Report only
   checks that actually completed.
