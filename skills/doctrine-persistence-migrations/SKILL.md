---
name: doctrine-persistence-migrations
description: Design, implement, test, and review Doctrine persistence, repositories, database constraints, and migrations in office_manager. Use whenever database mappings, schema, relationships, or stored data may change.
---

# Doctrine Persistence and Migrations — office_manager

## Required reading

Read:
- `docs/engineering/standards.md`
- `docs/engineering/architecture.md`
- `docs/engineering/database-migrations.md`
- `docs/engineering/testing-strategy.md` when tests are affected

Inspect the existing Doctrine mappings, repositories, migrations, and database
configuration before proposing a persistence change.

## Workflow

1. Identify the intended behaviour and affected domain rules.
2. Inspect the current mappings, migration history, actual schema, repositories,
   and relevant tests.
3. Identify affected records, relationships, constraints, and existing data.
4. Determine the required schema and data changes before generating a migration.
5. Implement the smallest coherent persistence change.
6. Review generated migration code and SQL carefully.
7. Add or update integration tests for persistence behaviour.
8. Verify migration status and schema consistency using appropriate project commands.
9. Review the final diff for unintended data loss or unrelated schema changes.

## Doctrine mappings and repositories

- Follow the existing project conventions for entities, repositories, and mappings.
- Keep mapping definitions consistent with the intended domain behaviour.
- Use appropriate foreign keys, indexes, uniqueness constraints, and nullability.
- Make relationship ownership explicit.
- Review cascade operations and orphan removal carefully.
- Avoid duplicate representations of the same state without a clear ownership strategy.
- Keep repository methods focused on queries and persistence responsibilities.
- Do not bypass domain invariants merely because an ORM operation permits it.

## Migration safety

- Treat migrations as versioned changes that may affect existing deployments.
- Review every generated migration before applying it.
- Do not rewrite migrations that have already been applied to shared environments.
- Preserve existing data unless removal or transformation is explicitly required.
- Assess locking, table size, transaction behaviour, and deployment order where relevant.
- Do not run destructive commands until the target environment has been verified.
- Do not use automatic schema updates as a replacement for reviewed migrations
  in shared or production environments.
- Do not assume a down migration can restore deleted or transformed data.

## Relationships and lifecycle rules

Before changing a relationship, establish:
- Whether it is optional or mandatory.
- Which side owns the association.
- What happens when either related record is archived or deleted.
- Whether historical data must remain available.
- Whether database constraints can enforce the required invariant.
- How concurrent operations affect the relationship.

Do not invent cascade, archival, deletion, or reactivation behaviour. Resolve
ambiguous domain requirements before changing persistence.

## Data transformations

For data migrations:
1. Define source and target states.
2. Identify duplicates, invalid records, and missing values.
3. Decide how partial failures and reruns are handled.
4. Test against representative data in an isolated environment.
5. Verify record counts and important invariants after execution.
6. Document irreversible changes and recovery requirements.

Never silently discard records that cannot be transformed.

## Testing and verification

- Use the configured test database for integration tests.
- Verify persistence and retrieval, not only method invocation.
- Test important constraints and relevant failure paths.
- Check migration status before and after migration operations as appropriate.
- Use the project's existing Composer scripts and migration commands.
- Never claim a migration or schema check passed unless it actually ran successfully.

## Completion criteria

Mappings, migration history, and the intended schema are consistent; existing
data is handled according to approved requirements; relevant integration tests
pass; and migration risks and recovery limitations are documented.
