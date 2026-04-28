# Orders Example — Walkthrough

A complete worked example demonstrating every skill capability: one aggregate, four events, two projectors (sync + queued), one reactor, and a full Pest test suite.

## What this example demonstrates

- **Gate 1 (Design)**: The user prompt in `01-user-prompt.md` triggers domain analysis. The skill produces an ADR (`02-adr.md`) covering the `OrderAggregate`, its commands, events, projectors, reactor, and invariants.
- **Gate 2 (Implementation)**: With the ADR approved, the skill generates 24 PHP files under `03-generated/`, following TDD — tests first, then implementation.

## Reading the output

### The ADR (`02-adr.md`)

Covers the full design: one aggregate (`OrderAggregate`), four commands, four events (past-tense, domain-meaningful), two projectors (`OrderProjector` sync, `DailyRevenueProjector` queued), one reactor (`SendShipConfirmationReactor`), and five invariants.

### Generated code (`03-generated/`)

```
app/Domain/Orders/
├── Aggregates/OrderAggregate.php          — Lifecycle + invariant enforcement
├── Commands/                              — 4 command DTOs (plain handler style)
├── CommandHandlers/                       — 4 handlers that load and persist the aggregate
├── Events/                                — 4 domain events (OrderPlaced, LineItemAdded, OrderShipped, OrderCancelled)
├── Exceptions/                            — 4 domain exceptions for invariant violations
├── Projectors/
│   ├── OrderProjector.php                 — Sync, builds the orders read model
│   └── DailyRevenueProjector.php          — Queued, tracks daily revenue stats
├── Reactors/
│   └── SendShipConfirmationReactor.php    — Sends email on OrderShipped
└── ReadModels/
    ├── Order.php                          — Order projection (extends Projection)
    └── DailyRevenueStat.php               — Daily revenue projection

tests/Feature/Orders/
├── OrderAggregateTest.php                 — 12 tests (happy paths + invariant violations)
├── OrderProjectorTest.php                 — 5 tests including idempotency
├── DailyRevenueProjectorTest.php          — 3 tests for revenue accumulation
└── SendShipConfirmationReactorTest.php    — 2 tests

database/migrations/
├── 2026_04_20_000001_create_orders_table.php
└── 2026_04_20_000002_create_daily_revenue_stats_table.php
```

### Key design decisions

| Decision | Rationale |
|---|---|
| Single `OrderAggregate` | Line items, shipping, and cancellation all affect the same consistency boundary |
| `OrderProjector` is sync | Order status must be visible immediately after command execution |
| `DailyRevenueProjector` is queued | Statistics are eventually consistent; no need to block the request |
| Reactor reads from Order read model | Sync projector runs first, so customer email is available without re-reading the aggregate |

## Running the tests yourself

1. Set up a fresh Laravel 11 project with `spatie/laravel-event-sourcing` v7 and Pest
2. Copy `03-generated/app/` to your project's `app/`
3. Copy `03-generated/tests/` to your project's `tests/`
4. Copy `03-generated/database/` to your project's `database/`
5. Register projectors and reactor in `config/event-sourcing.php`
6. Run `./vendor/bin/pest tests/Feature/Orders/`
