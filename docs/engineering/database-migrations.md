# Database Migrations and Persistence — office_manager

## 1. Purpose

This document defines how database changes, Doctrine mappings, and persistence
behaviour should be designed, reviewed, tested, and deployed.

It complements `standards.md`, `architecture.md`, and `testing-strategy.md`.

## 2. Inspect before changing the database

Before modifying persistence:
- Inspect the relevant Doctrine entities, repositories, mappings, and migrations.
- Determine the current database schema and the environments affected.
- Review foreign keys, indexes, unique constraints, nullability, and defaults.
- Identify existing data that could be affected.
- Check whether application code depends on the current schema or behaviour.
- Establish the intended result before generating a migration.

Do not assume the entity mappings, migration history, and actual database schema
are already consistent. Verify them.

## 3. Migration principles

- Treat every migration as a versioned database change.
- Review generated migration code before executing it.
- Prefer explicit, focused migrations over unrelated schema changes bundled
  together.
- Preserve existing data unless data removal or transformation is explicitly
  required and approved.
- Consider table size, locking, execution time, and deployment order.
- Do not rewrite an already-applied migration to change deployed history.
- Do not execute destructive migrations without understanding and approving
  their effects.
- Treat `down()` as potentially destructive: dropping a table or column can
  permanently remove data. For rollback paths that would delete populated
  records, guard the operation so it refuses to proceed unless data loss has
  been explicitly approved, or use a verified data-preserving recovery plan.
- State the data effects and limitations of every destructive `down()` in its
  description or accompanying deployment notes. A backup is useful only if its
  restoration path has been verified.
- Do not use automatic schema updates as a substitute for reviewed migrations
  in shared or production environments.

## 4. Entity and schema consistency

For every persistence change, consider consistency among:
- Doctrine entity mappings and attributes.
- Migration SQL and migration history.
- The actual database schema.
- Repository queries and application expectations.
- Database constraints and application-level validation.
- Fixtures and test database setup, where applicable.

Use appropriate database constraints to protect data integrity, including
foreign keys, unique constraints, and nullability rules where required.

Do not rely on application validation alone when a database constraint is
needed to protect against concurrent operations or alternative write paths.

## 5. Relationships and deletion behaviour

Before changing a relationship, explicitly determine:
- Whether the relationship is optional or mandatory.
- Which side owns the association.
- Whether deletion is restricted, cascaded, or handled explicitly.
- What happens to related records when an entity is archived or removed.
- Whether historical records must be retained.
- How the rules behave under concurrent requests.

Do not add cascade deletion, orphan removal, or nullable foreign keys without
confirming that they match the domain requirements.

Archiving and deletion are not interchangeable. Preserve the distinction
required by the domain and its historical-data rules.

## 6. Transactions and data integrity

For operations involving multiple related writes:
- Identify which changes must succeed or fail together.
- Use an appropriate transaction boundary.
- Consider database constraints and concurrent modifications.
- Handle failures without leaving unintended partial state.
- Make retry behaviour explicit when an operation may be repeated.
- Verify preconditions again when a delayed operation resumes.

Do not claim that a workflow is atomic merely because it runs within one
application service. Verify the actual transaction and infrastructure
boundaries.

## 7. Data migrations and transformations

When existing data must be transformed:
1. Define the source and target states.
2. Identify invalid, duplicate, missing, or unexpected records.
3. Determine whether the transformation is reversible.
4. Define how partial failure and reruns are handled.
5. Test against representative data.
6. Verify counts, constraints, and important invariants after execution.
7. Document any irreversible operation and required recovery procedure.

Do not silently discard records that cannot be transformed. Report them and
use an explicitly agreed recovery or remediation strategy.

## 8. Testing persistence changes

For relevant changes, test:
- Entity persistence and retrieval.
- Relationship ownership and lifecycle behaviour.
- Unique and foreign-key constraints.
- Nullability and invalid data.
- Transaction and failure behaviour where applicable.
- Migration effects on representative existing data.
- Compatibility between application code and the resulting schema.

Use an isolated test database. Verify the configured environment before
running destructive commands.

## 9. Deployment and recovery

Before recommending a database change for deployment:
- Review the migration and its expected effects.
- Confirm required backups and recovery procedures for the risk involved.
- Consider whether the application and schema can be deployed in a safe order.
- Identify downtime or locking risks.
- Determine how success will be verified.
- Explain rollback limitations, especially when data has been transformed or
  deleted.

A down migration is not automatically a safe recovery strategy. Restoring
deleted or transformed data may require a verified backup or a forward repair.

The committed Company table migration (`Version20261026131000`) drops the
`company` table in `down()`. Keep this applied migration unchanged. Do not roll
it back unless Company data has first been exported or a backup restore has
been verified and rollback has explicit approval. Prefer a forward migration
when Company records must be retained.

## 10. Completion criteria

A persistence change is ready when:
- The intended schema and data effects are understood.
- Entity mappings and migration history are consistent with the design.
- Existing data is preserved or transformed according to approved requirements.
- Relevant persistence and regression tests pass.
- Migration execution and recovery risks have been reviewed.
- The final diff contains no accidental schema or migration changes.
