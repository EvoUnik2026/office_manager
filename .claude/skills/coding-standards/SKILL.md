---
name: coding-standards
description: >
  Use when writing, editing, or reviewing PHP code in office_manager.
  Follow docs/engineering/standards.md and the configured project tools as
  the source of truth for code conventions and quality gates.
---

# Coding Standards

## Required reading

Read `docs/engineering/standards.md`. The detailed rules live there; this
skill describes how to apply and verify them without duplicating them.

## Workflow

1. Inspect `app/.php-cs-fixer.dist.php`, `app/phpstan.dist.neon`, and
   `app/phpunit.dist.xml` when the relevant tooling or test setup is involved.
2. Make the smallest focused change that follows the authoritative engineering
   standards.
3. Run the relevant configured Composer checks from `app/` or the PHP
   container:
   - `composer cs`
   - `composer stan`
   - `composer test:unit`, `composer test:integration`, or `composer test`
4. Report checks that could not be run; do not claim unverified success.
