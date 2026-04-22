---
name: laravel-spatie-event-sourcing
description: >
  Use this skill any time a user works with Laravel and event sourcing together
  — whether setting up spatie/laravel-event-sourcing for the first time,
  designing a new event-sourced domain (aggregates, events, projectors,
  reactors), generating code for bounded contexts, or debugging issues with
  AggregateRoot, StoredEvent, or projectors. Trigger on: "event sourcing" +
  "Laravel", spatie ES references, requests to model domain lifecycles as
  events, or questions about aggregate roots and projectors in PHP/Laravel. Do
  NOT use for: refactoring existing CRUD into ES, Laravel's built-in
  Event::dispatch system, CQRS without event sourcing, or non-spatie packages.
---

# Laravel Spatie Event Sourcing — Skill

Generate event-sourced domain code for Laravel using `spatie/laravel-event-sourcing`, following a two-gate workflow: **Design first, then implement with tests**.

## Quick reference

- `references/design-heuristics.md` — Event naming, aggregate boundaries, projector/reactor rules, anti-patterns
- `references/adr-template.md` — ADR template for Gate 1
- `references/tdd-patterns.md` — Pest and PHPUnit test patterns
- `references/commands-pattern.md` — Command + handler patterns (plain and bus-dispatched)
- `references/spatie-api-cheatsheet.md` — Key Spatie ES classes and methods
- `assets/templates/*.stub` — Code generation stubs

## Project bootstrap (one-time, automatic)

On first invocation in a project, run `scripts/verify-setup.sh` from this skill's directory. It checks:

0. PHP 8.2+ is installed (required by `spatie/laravel-event-sourcing` v7)
1. `spatie/laravel-event-sourcing` is in `composer.json`
2. The `stored_events` and `snapshots` tables exist (migrations have run)
3. `config/event-sourcing.php` has been published
4. A test framework is installed (Pest preferred, PHPUnit fallback)

If anything is missing, print the single command that fixes it and **stop**. Do not auto-install packages or run migrations on behalf of the user — they need to confirm first.

If everything passes, check for `.claude/event-sourcing.md` in the project root. If it doesn't exist, ask the user **one** configuration question:

> "I'll create a project config at `.claude/event-sourcing.md`. Two quick choices:
> 1. **Command dispatch style**: plain handler classes invoked directly, or Laravel's command bus (`Bus::dispatch`)?
> 2. **Test framework**: Pest or PHPUnit?"

Write their answers to that file. On future invocations, read it silently and proceed.

---

## The two-gate workflow

This skill operates in two gates with a hard stop between them. The user must approve the design before any code gets written.

### Gate 1 — Design (ADR)

Start with domain analysis. Ask 3–5 focused questions (skip any the user already answered):

1. What feature or bounded context are we building? (one sentence)
2. What state does it hold, and what state transitions matter?
3. What external actions trigger those transitions?
4. What read models does the application need?
5. What side effects happen on state changes? (emails, external API calls, other aggregates)

Then produce an ADR using the template in `references/adr-template.md`. The ADR covers:

- Proposed aggregate(s) with justification for each boundary
- Commands that trigger each aggregate method
- Events recorded by each method (past-tense, domain-meaningful names — read `references/design-heuristics.md` for naming rules)
- Projectors and their read models (sync vs queued, with reasoning)
- Reactors and their side effects
- Invariants the aggregate enforces
- Explicitly punted concerns (data migration, snapshots, etc.)
- Anti-patterns actively avoided

**Stop here.** Present the ADR and wait for the user to approve, request changes, or edit inline. Iterate until they say something like "approved", "looks good", or "let's build it."

Do not write any implementation code during Gate 1.

### Gate 2 — Implementation + verification

Once the ADR is approved, generate everything in one uninterrupted flow. No mid-implementation questions — if something is ambiguous, that's an ADR defect; go back to Gate 1.

Generation order:

1. Create the directory structure under `app/Domain/<Context>/`
2. Write tests FIRST (Pest or PHPUnit per project config) — aggregate tests, projector tests, reactor tests
3. Write command DTOs
4. Write command handlers (plain or bus-dispatched per project config)
5. Write the aggregate root
6. Write event classes
7. Write projector(s) and read-model migration(s)
8. Write reactor(s)
9. Register projectors and reactors in `config/event-sourcing.php`
10. Run the test suite (`./vendor/bin/pest` or `./vendor/bin/phpunit`)
11. Report: list of files created + test output

Use the stub templates in `assets/templates/` as starting points. Read `references/tdd-patterns.md` for test structure and `references/commands-pattern.md` for command/handler patterns.

**Stop here.** Present the results. The user approves, requests changes, or moves to the next feature.

## Directory conventions

```
app/Domain/<Context>/
├── Aggregates/
│   └── <Name>Aggregate.php
├── Commands/
│   └── <Verb><Noun>Command.php
├── CommandHandlers/
│   └── <Verb><Noun>Handler.php
├── Events/
│   └── <Noun><PastTenseVerb>.php
├── Projectors/
│   └── <Noun>Projector.php
├── Reactors/
│   └── Send<Noun><Action>Reactor.php
└── ReadModels/
    └── <Noun>.php

tests/Feature/<Context>/
├── <Name>AggregateTest.php
├── <Noun>ProjectorTest.php
└── <Noun>ReactorTest.php

database/migrations/
└── <timestamp>_create_<read_model>_table.php
```

## Scope boundary

This skill is for **greenfield event sourcing only**. If the user asks to refactor existing CRUD code into event sourcing, politely decline and explain that CRUD-to-ES migration is a different (and much harder) problem that this skill doesn't cover. Suggest they design the event-sourced version from scratch alongside the existing code instead.

## When things go wrong

- **Tests fail after generation**: Read the failure output, fix the code, rerun. Don't ask the user to debug generated code — that's our job.
- **Ambiguity during Gate 2**: Return to Gate 1 and update the ADR. Don't make assumptions mid-implementation.
- **User wants to change the design after seeing code**: No problem — update the ADR, regenerate the affected files, rerun tests.
