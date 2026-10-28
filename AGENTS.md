# Instructions for Coding Agents — office_manager

## 1. Required reading

Before planning or implementing a change, read:

1. `docs/engineering/standards.md`
2. The relevant supporting documents:
   - `docs/engineering/architecture.md`
   - `docs/engineering/testing-strategy.md`
   - `docs/engineering/database-migrations.md`
   - `docs/engineering/definition-of-done.md`

Read the relevant files under `skills/` when they exist and apply to the task.

The engineering standards are the source of truth. Skills provide task-specific
workflows and must not override those standards.

## 2. Repository structure

- The repository root contains project-wide instructions and documentation.
- `app/` contains the Symfony application and its Composer configuration.
- `docker/` and `docker-compose.yaml` define the container environment.
- Engineering documentation lives in `docs/engineering/`.
- Reusable engineering skills live in `skills/`.

Inspect the actual repository before relying on this description. Do not assume
that a path, service, or file exists without checking it.

## 3. Safe working rules

- Inspect relevant implementation, tests, configuration, and Git status first.
- Preserve all existing and uncommitted work, including work unrelated to the task.
- Do not discard, reset, overwrite, or stage unrelated changes.
- Do not edit files outside the agreed scope without explaining why.
- Ask for clarification when a missing requirement materially affects correctness,
  security, data integrity, or an irreversible operation.
- Prefer small, reviewable changes over large batches.
- Do not invent domain rules or silently change agreed behaviour.
- Do not add dependencies, tools, abstractions, or architecture without a
  concrete justification.
- Never expose or commit secrets, credentials, or sensitive production data.

## 4. Implementation workflow

1. Summarize the requested outcome and acceptance criteria.
2. Inspect the current implementation and identify relevant dependencies.
3. Propose a focused plan and identify risks.
4. Implement incrementally, adding or updating tests with the behaviour.
5. Run appropriate quality checks.
6. Review the final diff and report the actual results.

For multi-step work, present small, logical steps and verify important results
before proceeding.

## 5. Quality gates

Use the application's existing Composer scripts from the PHP container when
appropriate:

- `docker compose exec php composer cs`
- `docker compose exec php composer stan`
- `docker compose exec php composer test:unit`
- `docker compose exec php composer test:integration`
- `docker compose exec php composer test`

Run the narrowest relevant test first. Run the full suite when warranted by the
scope and risk of the change.

Do not claim a command passed unless it was actually executed successfully.
If a check cannot run, explain why.

## 6. Database safety

- Inspect entity mappings, migrations, and the target database before changing
  persistence.
- Verify the environment before running destructive or data-changing commands.
- Never use development or production data as a test database.
- Review migrations before applying them.
- Preserve existing data unless an explicit requirement authorizes a change.
- Consider constraints, transactions, concurrency, and recovery.

## 7. Completion

Use `docs/engineering/definition-of-done.md` before declaring work complete.

Summarize:
- What changed and why.
- Tests and quality checks run, with actual results.
- Relevant database, security, or operational considerations.
- Remaining risks, limitations, and follow-up work.
