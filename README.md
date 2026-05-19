# Laravel Spatie Event Sourcing — Claude Code Skill

![Release](https://img.shields.io/github/v/release/albertoarena/claude-laravel-event-sourcing?style=for-the-badge)
![License](https://img.shields.io/github/license/albertoarena/claude-laravel-event-sourcing?style=for-the-badge)
![Code size](https://img.shields.io/github/languages/code-size/albertoarena/claude-laravel-event-sourcing?style=for-the-badge)
![Claude Code Skill](https://img.shields.io/badge/Claude%20Code-Skill-8A63D2?style=for-the-badge)

## Compatibility

| Requirement                       | Version                  |
|-----------------------------------|--------------------------|
| PHP                               | 8.2+                     |
| Laravel                           | 10.x, 11.x, 12.x, 13.x   |
| `spatie/laravel-event-sourcing`   | ^7.0                     |
| Claude Code                       | any                      |

A Claude Code skill that helps you design and generate event-sourced domain code for Laravel using [spatie/laravel-event-sourcing](https://github.com/spatie/laravel-event-sourcing).

## What it does

This skill guides you through a **two-gate workflow**:

1. **Gate 1 — Design**: Claude asks focused domain questions, then produces an Architecture Decision Record (ADR) covering aggregates, commands, events, projectors, and reactors. You review and approve before any code is written.

2. **Gate 2 — Implementation**: Once the ADR is approved, Claude generates the full domain — tests first (TDD), then commands, handlers, aggregates, events, projectors, reactors, and migrations. It runs the test suite and reports results.

## Installation

### Option A: Per-project (recommended for teams)

Add the skill to your Laravel project so it's shared via git with your team:

```bash
# From your Laravel project root
mkdir -p .claude/skills
git clone https://github.com/albertoarena/claude-laravel-event-sourcing.git /tmp/claude-les
cp -r /tmp/claude-les/skill/laravel-spatie-event-sourcing .claude/skills/
rm -rf /tmp/claude-les
```

The skill will be at `.claude/skills/laravel-spatie-event-sourcing/SKILL.md` inside your project. Claude Code picks it up automatically — no restart needed.

### Option B: Skill package (simplest)

Download the `.skill` package from the repo and add it directly in Claude Code:

```bash
curl -LO https://github.com/albertoarena/claude-laravel-event-sourcing/raw/main/laravel-spatie-event-sourcing.skill
```

Then install it via Claude Code's `/install-skill` command or place it in `~/.claude/skills/`.

### Option C: Global (all your projects)

Clone the full repo for all your projects:

```bash
cd ~/.claude/skills
git clone https://github.com/albertoarena/claude-laravel-event-sourcing.git
```

The skill is at `~/.claude/skills/claude-laravel-event-sourcing/skill/laravel-spatie-event-sourcing/SKILL.md`.

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
