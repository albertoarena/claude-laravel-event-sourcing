# How the Two-Gate Workflow Works

This skill uses a deliberate two-step process: **design first, then implement**. No code is written until you've reviewed and approved the design.

## Before you start

On first use in a project, the skill checks that your Laravel project is ready:

- PHP 8.2+ installed
- `spatie/laravel-event-sourcing` in `composer.json`
- Stored events and snapshots migrations published
- `config/event-sourcing.php` published
- A test framework installed (Pest or PHPUnit)

If anything is missing, it tells you the exact command to fix it and stops. It never installs or runs things on your behalf.

Once everything passes, it asks you two one-time configuration questions:

1. **Command dispatch style** — plain handler classes (default) or Laravel's command bus?
2. **Test framework** — Pest or PHPUnit?

Your answers are saved to `.claude/event-sourcing.md` and used silently from then on.

## Gate 1 — Design

The skill starts by understanding your domain. It asks 3-5 focused questions:

1. What feature or bounded context are we building?
2. What state does it hold, and what state transitions matter?
3. What external actions trigger those transitions?
4. What read models does the application need?
5. What side effects happen on state changes?

It skips any questions you've already answered in your prompt.

From your answers, it produces an **Architecture Decision Record (ADR)** that covers:

- **Aggregates** — what state changes atomically, and why each aggregate is its own boundary
- **Commands** — what triggers each aggregate method
- **Events** — what gets recorded (past-tense, domain-meaningful names like `OrderShipped`, never `OrderUpdated`)
- **Projectors** — what read models are built, whether sync or queued, and why
- **Reactors** — what side effects happen (emails, API calls, etc.)
- **Invariants** — what rules the aggregate enforces
- **Out of scope** — what's explicitly punted (snapshots, data migration, etc.)
- **Anti-patterns avoided** — what the design deliberately doesn't do

**The skill stops here.** You review the ADR and either approve it, request changes, or edit it inline. Nothing gets built until you say something like "approved" or "looks good."

## Gate 2 — Implementation

Once you approve the ADR, the skill generates everything in one uninterrupted flow:

1. Creates the directory structure under `app/Domain/<Context>/`
2. Writes tests first (TDD) — aggregate, projector, and reactor tests
3. Writes command DTOs
4. Writes command handlers
5. Writes the aggregate root
6. Writes event classes
7. Writes projectors and read-model migrations
8. Writes reactors
9. Registers projectors and reactors in `config/event-sourcing.php`
10. Runs the test suite
11. Reports results — list of files created and test output

There are no mid-implementation questions. If something is ambiguous, that's an ADR defect — the skill goes back to Gate 1 rather than guessing.

**The skill stops here.** You review the generated code and test results, then approve, request changes, or move on to the next feature.

## What gets generated

```
app/Domain/<Context>/
├── Aggregates/          — Aggregate roots with invariant enforcement
├── Commands/            — Command DTOs (plain or bus-dispatchable)
├── CommandHandlers/     — Handlers that load aggregates and call methods
├── Events/              — Domain events (immutable value objects)
├── Projectors/          — Event-to-read-model transformers
├── Reactors/            — Side-effect handlers
└── ReadModels/          — Projection models for read-side queries

tests/Feature/<Context>/ — Full test suite
database/migrations/     — Read-model table migrations
```

## When things go wrong

- **Tests fail after generation** — The skill reads the failure output, fixes the code, and reruns. You don't need to debug generated code.
- **Ambiguity during implementation** — The skill returns to Gate 1 to update the ADR rather than making assumptions.
- **You want to change the design after seeing code** — No problem. The ADR gets updated, affected files are regenerated, and tests are rerun.

## Scope

This skill is for **greenfield event sourcing only**. It does not refactor existing CRUD code into event sourcing — that's a fundamentally different (and harder) problem. If you have existing code, the recommended approach is to design the event-sourced version from scratch alongside it.
