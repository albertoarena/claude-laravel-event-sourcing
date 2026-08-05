# Laravel Spatie Event Sourcing

A Claude Code plugin that helps you design and generate event-sourced domain code
for Laravel using [spatie/laravel-event-sourcing](https://github.com/spatie/laravel-event-sourcing).

## What it does

The plugin bundles a skill that guides you through a **two-gate workflow**:

1. **Gate 1 — Design.** Claude asks focused domain questions, then produces an
   Architecture Decision Record (ADR) covering aggregates, commands, events,
   projectors, and reactors. You review and approve before any code is written.
2. **Gate 2 — Implementation.** Once the ADR is approved, Claude generates the
   full domain — tests first (TDD), then commands, handlers, aggregates, events,
   projectors, reactors, and migrations — and runs the suite.

## Install

```bash
/plugin marketplace add albertoarena/claude-laravel-event-sourcing
/plugin install laravel-spatie-event-sourcing@albertoarena
```

Then start a Laravel project session and describe the domain you want, e.g.:

> "I want to add order management using Spatie event sourcing. Orders can be
> placed, have line items added, shipped, and cancelled — but not if already
> shipped."

## Compatibility

| Requirement                     | Version                |
|---------------------------------|------------------------|
| PHP                             | 8.2+                   |
| Laravel                         | 10.x, 11.x, 12.x, 13.x |
| `spatie/laravel-event-sourcing` | ^7.0                   |
| Claude Code                     | any                    |

## Scope

- **Greenfield only** — generates new event-sourced domains; does not refactor
  existing CRUD into event sourcing.
- **Laravel + Spatie ES v7** — specifically designed for this stack.

See the [repository](https://github.com/albertoarena/claude-laravel-event-sourcing)
for full documentation and a worked example.

## License

MIT
