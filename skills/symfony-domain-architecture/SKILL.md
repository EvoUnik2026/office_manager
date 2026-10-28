---
name: symfony-domain-architecture
description: Design and modify Symfony application architecture and domain behaviour in office_manager. Use when implementing business rules, domain models, use cases, repositories, or cross-module workflows.
---

# Symfony Domain Architecture — office_manager

## Required reading

Read:
- `docs/engineering/standards.md`
- `docs/engineering/architecture.md`

Read `docs/engineering/database-migrations.md` when persistence is affected.
Read relevant domain decisions and tests before changing existing behaviour.

## Workflow

1. Identify the use case, acceptance criteria, and affected domain rules.
2. Inspect existing domain classes, application services, entities, repositories,
   controllers, commands, tests, and dependency configuration as relevant.
3. Map the current responsibilities and dependency directions before proposing
   architectural changes.
4. Identify the authoritative owner of each business invariant.
5. Make the smallest coherent change that preserves existing behaviour outside
   the agreed scope.
6. Add or update tests for valid transitions, invalid transitions, and relevant
   interactions.
7. Review the final diff for unintended coupling and unrelated refactoring.

## Domain model

- Represent business concepts and rules explicitly.
- Keep important invariants enforceable rather than relying only on UI validation.
- Make state transitions explicit and reject invalid transitions.
- Distinguish archival, deactivation, deletion, and terminal states according
  to the confirmed domain requirements.
- Do not invent rules from class names, database columns, or assumptions.
- Ask for clarification when an unresolved rule affects correctness, related
  records, or data integrity.
- Keep domain decisions independent of HTTP, console output, and transport
  details where practical.

## Application layer

- Model application services around clear use cases.
- Coordinate domain behaviour and infrastructure through explicit dependencies.
- Keep orchestration separate from business rules that belong in the domain.
- Define appropriate transaction boundaries for operations affecting multiple
  records or steps.
- Consider concurrency, retries, idempotency, and partial failure where relevant.
- Revalidate preconditions before resuming delayed or retried workflows.

## Symfony and persistence boundaries

- Keep controllers focused on request handling, HTTP responses, and use-case
  coordination.
- Use dependency injection for application and infrastructure services.
- Inspect the project's existing Entity and Repository patterns before
  introducing new ones.
- Do not assume every Doctrine entity needs a separate domain object.
- Avoid duplicate representations of state without a clear ownership and
  synchronization strategy.
- Use interfaces when they provide meaningful dependency inversion, isolation,
  or alternative implementations; do not create one for every class.
- Do not place unrelated business workflows in repositories.

## Cross-entity workflows

For operations that affect related objects:
1. Identify all affected entities and parent-child relationships.
2. Determine the invariants that must hold before and after the operation.
3. Define what must happen atomically and what may require retry or recovery.
4. Consider conflicting operations and concurrent modifications.
5. Test both successful execution and relevant failure or recovery paths.

Do not check only the directly requested entity when related state can make an
operation invalid.

## Verification

Run relevant tests and quality checks:
- `docker compose exec php composer cs`
- `docker compose exec php composer stan`
- `docker compose exec php composer test`

Run the checks appropriate to the change and report actual results.

## Completion criteria

The use case meets its acceptance criteria, domain invariants remain enforced,
dependencies follow intentional boundaries, relevant tests pass, and the final
diff contains no unrelated changes.
