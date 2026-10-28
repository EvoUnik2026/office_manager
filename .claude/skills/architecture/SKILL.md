---
name: architecture
description: >
  Use when designing or changing domain models, application workflows, or
  persistence boundaries in office_manager. Follow docs/engineering/architecture.md
  as the source of truth for architecture and domain invariants.
---

# Architecture & Domain Model Skill

## Required reading

Read `docs/engineering/standards.md` and
`docs/engineering/architecture.md` before architectural changes. Consult the
testing strategy and migration guidance when those areas are affected.

## Workflow

1. Inspect the relevant implementation, tests, dependency configuration, and
   persistence mappings.
2. Identify the owner of each business invariant and the relevant dependency
   direction.
3. Confirm unresolved domain behaviour before changing it.
4. Prefer the smallest change that preserves existing behaviour.
5. For pending lifecycle workflows, identify conflicting operations and
   revalidate preconditions from authoritative state whenever the workflow
   resumes.
6. Add focused tests at the unit or integration boundary appropriate to the
   behaviour.
7. Review the final diff for unintended coupling or unrelated changes.
