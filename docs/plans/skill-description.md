# Sub-plan: Update SKILL.md Description

**Parent:** [backlog.md](./backlog.md) — M4
**Status:** PENDING DECISION

## Problem

The current SKILL.md description may under-trigger because it lacks explicit trigger phrases for natural user prompts like "let's event-source X" or "model this with aggregates."

## Proposed change

Replace the YAML `description` in `SKILL.md` frontmatter (lines 2-12).

### Current

```yaml
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
```

### Proposed

```yaml
description: >
  Design and generate Spatie event-sourcing code for Laravel projects, using a
  TDD-first, human-in-the-loop workflow. Use this skill whenever the user wants
  to add event-sourced features to a Laravel app, mentions Spatie event
  sourcing, asks about aggregates, events, projectors, reactors, or commands
  in a Laravel context, wants to design a new bounded context with event
  sourcing, or says things like "let's event-source X," "add ES to my Laravel
  project," or "I want to use spatie/laravel-event-sourcing." Also use this
  skill when the user is setting up aggregates, writing projectors, wiring
  reactors, or generating commands + command handlers for a Laravel
  event-sourced domain, even if they don't explicitly name the skill. Do NOT
  use for: refactoring existing CRUD into ES, Laravel's built-in
  Event::dispatch system, CQRS without event sourcing, or non-spatie packages.
  Scope is greenfield only.
```

## What changes

| Aspect | Current | Proposed |
|---|---|---|
| Lead-in | Generic ("Use this skill any time") | Purpose-driven ("Design and generate") |
| Explicit trigger phrases | None | "let's event-source X", "add ES to my Laravel project", "I want to use spatie/..." |
| Implicit triggers | None | "even if they don't explicitly name the skill" |
| Activity-based triggers | None | Setting up aggregates, writing projectors, wiring reactors |
| Debugging use case | Included | Dropped (skill is for generation, not debugging) |
| Negative boundaries | Included | Kept, moved to end |
| Greenfield reinforcement | Not in description | Added as final sentence |

## What stays the same

- The `Do NOT use for:` exclusion list (refactoring, Event::dispatch, CQRS-only, non-spatie)
- Greenfield-only scope (reinforced in both description and SKILL.md body at line 131)

## Trade-offs

**Pros:**
- Better activation on natural language prompts
- Matches the spec's intent (skill-spec.md section 2)
- Keeps the guardrails

**Cons:**
- Longer description
- Drops the "debugging issues" trigger — but the skill wasn't designed for debugging, so this is arguably correct
- Hard to verify without real-world usage testing

## Open question

Has under-triggering been observed in practice? If the skill already triggers reliably, this change adds risk (unintended over-triggering) without clear benefit.
