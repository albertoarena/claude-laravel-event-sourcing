# Laravel Spatie Event Sourcing — Claude Code Skill

A Claude Code skill that helps you design and generate event-sourced domain code for Laravel using [spatie/laravel-event-sourcing](https://github.com/spatie/laravel-event-sourcing).

## What it does

This skill guides you through a **two-gate workflow**:

1. **Gate 1 — Design**: Claude asks focused domain questions, then produces an Architecture Decision Record (ADR) covering aggregates, commands, events, projectors, and reactors. You review and approve before any code is written.

2. **Gate 2 — Implementation**: Once the ADR is approved, Claude generates the full domain — tests first (TDD), then commands, handlers, aggregates, events, projectors, reactors, and migrations. It runs the test suite and reports results.

## Installation

```bash
# Clone into your Claude Code skills directory
cd ~/.claude/skills
git clone https://github.com/albertoarena/claude-laravel-event-sourcing.git
```

The skill is at `skill/laravel-spatie-event-sourcing/SKILL.md`.

## Prerequisites

Your Laravel project needs:

- Laravel 10+ with `spatie/laravel-event-sourcing` v7 installed
- Published migrations and config (`php artisan vendor:publish`)
- A test framework (Pest recommended, PHPUnit supported)

The skill checks all of this automatically on first run.

## Quick start

Open Claude Code in your Laravel project and say something like:

> "I want to add order management using Spatie event sourcing. Orders can be placed, have line items added, shipped, and cancelled — but not if already shipped."

Claude will walk you through the design, get your approval, then generate all the code with tests.

## What gets generated

```
app/Domain/<Context>/
├── Aggregates/          — Aggregate roots with invariant enforcement
├── Commands/            — Command DTOs
├── CommandHandlers/     — Handlers that load aggregates and call methods
├── Events/              — Domain events (past-tense, immutable)
├── Projectors/          — Event-to-read-model transformers
├── Reactors/            — Side-effect handlers (emails, API calls)
└── ReadModels/          — Eloquent models for read-side queries

tests/Feature/<Context>/ — Full test suite (aggregate, projector, reactor tests)
database/migrations/     — Read-model table migrations
```

## Scope

- **Greenfield only** — This skill generates new event-sourced domains. It does not refactor existing CRUD code into event sourcing.
- **Laravel + Spatie ES v7** — Specifically designed for this stack.

## License

MIT
