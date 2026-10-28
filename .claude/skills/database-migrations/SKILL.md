---
name: database-migrations
description: >
  Use when changing Doctrine mappings, database schemas, persistence, or
  migrations in office_manager. Follow docs/engineering/database-migrations.md
  as the source of truth for data safety and migration policy.
---

# Database Migrations

## Required reading

Read `docs/engineering/standards.md`,
`docs/engineering/database-migrations.md`, and the related architecture and
testing documents when those areas are affected.

## Workflow

1. Inspect entity mappings, repositories, migrations, database configuration,
   and affected data before changing persistence.
2. Generate or write a focused migration that matches the approved schema
   change; review both `up()` and `down()` before applying it.
3. Never alter a migration already applied to any environment. Check migration
   status and deployment history first.
4. Treat destructive rollback SQL as data deletion, not as a safe undo. Guard
   rollback of populated records or use a verified, explicitly approved
   recovery plan; document irreversible effects.
5. Use an isolated test database. Run relevant migration, schema validation,
   and integration checks.
6. Review the final diff for accidental migration changes and unintended data
   loss.
