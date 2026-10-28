# Architecture Guidelines — office_manager

## 1. Purpose

These guidelines describe how to design and evolve the architecture of
office_manager. They complement `standards.md` and must remain consistent with
it.

The goal is maintainable, testable software with explicit business rules—not
architectural complexity for its own sake.

## 2. Inspect before designing

Before changing architecture:
- Inspect the existing implementation, tests, dependency configuration, and
  persistence mappings relevant to the change.
- Identify the existing responsibilities and dependency directions.
- Check whether the requirement can be met without adding another layer or
  introducing a new abstraction.
- Preserve existing behaviour unless the requirement explicitly changes it.
- Explain the benefits and costs of significant architectural changes.

Do not assume every existing class follows the intended architecture perfectly.
Improve boundaries incrementally when there is a concrete reason.

## 3. Responsibilities of the main layers

### Domain

The domain represents business concepts, rules, invariants, and state
transitions.

- Keep business decisions in domain objects or domain services when appropriate.
- Reject invalid state transitions explicitly.
- Avoid coupling domain decisions to HTTP requests, console output, or messaging
  transport.
- Keep the domain independent of infrastructure where practical.
- Do not create interfaces or domain services without a demonstrated need.

### Application

The application layer coordinates use cases.

- Express the intention of a use case clearly.
- Coordinate domain behaviour and required ports or infrastructure services.
- Keep orchestration separate from business rules that belong in the domain.
- Handle application-level transactions and workflow coordination at an
  appropriate boundary.
- Avoid turning application services into large classes that contain unrelated
  operations.

### Persistence and infrastructure

Persistence and infrastructure code implement technical details such as
database access, message transport, and external integrations.

- Keep persistence concerns out of domain logic where practical.
- Use repositories or other appropriate abstractions for data access.
- Keep Doctrine mapping and lifecycle behaviour explicit.
- Do not bypass domain invariants merely because an ORM operation makes it easy.
- Consider transactions, concurrency, constraints, and failure handling.

### Entities and repositories

Inspect the existing meaning of `Entity` and `Repository` in this project
before introducing new patterns.

- Do not assume every Doctrine entity must be a domain entity or vice versa.
- Avoid maintaining duplicate representations of the same state without a
  clear ownership and synchronization strategy.
- Keep repository methods intention-revealing and focused on queries or
  persistence responsibilities.
- Do not put unrelated business workflows in repositories.
- Document and test any deliberate separation between domain objects and
  persistence entities.

### Controllers and user interfaces

- Keep controllers focused on transport concerns, request handling, and
  coordinating the appropriate application use case.
- Validate request structure and input at the boundary.
- Do not rely on browser-side validation for business invariants or security.
- Keep business decisions out of templates and UI event handlers.
- Return appropriate responses and avoid exposing internal exception details.

### Commands and asynchronous processing

- Keep console commands and message handlers focused on transport and
  orchestration.
- Reuse application use cases instead of duplicating business logic.
- Make retries, duplicate delivery, partial failure, and idempotency explicit
  where relevant.
- Do not assume a message is processed exactly once unless the infrastructure
  guarantees it.

## 4. Dependency direction

- Dependencies should be explicit and easy to follow.
- Avoid circular dependencies between modules and layers.
- Prefer dependency injection for application and infrastructure services.
- Domain code should not depend on controllers or transport-specific details.
- Introduce interfaces at meaningful boundaries, especially where independent
  implementations, isolation in tests, or dependency inversion provide value.
- Do not add an interface for every class by default.

## 5. Business rules and domain invariants

- Identify the authoritative owner of each business rule.
- Enforce important invariants in a place that cannot be bypassed by ordinary
  application paths.
- Treat database constraints as complementary protection, not a replacement
  for clear domain behaviour.
- Review related entities and workflows when a state change affects more than
  one object.
- Make lifecycle transitions and terminal states explicit.
- Do not infer missing domain decisions; ask for clarification when they affect
  correctness or data integrity.

## 6. Transactions, workflows, and concurrency

For operations involving multiple records or steps:
- Define the consistency boundary.
- Determine which steps must be atomic.
- Consider concurrent requests and stale state.
- Define retry and recovery behaviour where failures can leave work incomplete.
- Revalidate preconditions before resuming delayed or retried workflows.
- Avoid claiming that an operation is atomic or idempotent without verifying it.

Document significant workflow decisions and test relevant failure paths.

For lifecycle workflows that remain pending across requests or asynchronous
steps:
- Define which commands conflict with the pending transition and reject or
  serialize those commands while the transition is pending.
- Store or otherwise enforce the pending state at an authoritative boundary;
  do not rely only on a UI flag or a stale request.
- When a workflow resumes, reload the current state and revalidate every
  precondition before applying the transition.
- Make retries and duplicate delivery safe, and test conflicting commands,
  changed preconditions, and resumption after failure.

### Company lifecycle invariants

The initial Company model supports only a required, immutable registration
number and a required, editable name. Registration numbers remain reserved
after archival. Archiving is allowed only after both a direct Building–Company
association and any active Company–Room assignment have ended. Reactivation
restores the existing Company record, not its former associations. Repeated
archive and reactivate transitions are rejected.

## 7. Architecture changes

Before introducing a new layer, abstraction, module, or dependency, explain:
1. The problem with the current design.
2. The proposed responsibility and boundary.
3. Why a smaller change is insufficient.
4. The impact on testing, maintenance, and migration.
5. The risks and how they will be verified.

Prefer incremental changes over broad refactoring mixed into feature work.

## 8. Verification

For architecture-related changes:
- Test business rules independently where practical.
- Test interactions across application and infrastructure boundaries.
- Review dependency direction and unintended coupling.
- Run the relevant test suites, PHP-CS-Fixer, and PHPStan.
- Update architecture documentation when an intentional design decision changes.
