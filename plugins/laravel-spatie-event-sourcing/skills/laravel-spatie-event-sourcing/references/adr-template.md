# ADR Template

Use this template for Gate 1 output. Fill in every section — if something is genuinely not applicable, say so explicitly rather than leaving it blank.

```markdown
# ADR: <Feature> via event sourcing

**Status:** Proposed · <date>
**Scope:** <one sentence describing the bounded context>

## Context

<Why event sourcing for this feature? What problem does it solve? What would be lost with plain CRUD?>

## Aggregates

### `<Name>Aggregate`
- **Consistency boundary:** <what state must change atomically>
- **Identity:** <UUID strategy — typically `Str::uuid()` or passed in by the caller>

(Repeat for each aggregate. Justify why each is a separate aggregate if there are multiple.)

## Commands

| Command | Handler | Aggregate method |
|---|---|---|
| `PlaceOrderCommand` | `PlaceOrderHandler` | `OrderAggregate::place()` |

## Events

| Event | Recorded by | Payload |
|---|---|---|
| `OrderPlaced` | `OrderAggregate::place()` | orderId, customerId, items |

## Projectors

| Projector | Handles | Read model | Sync/Queued | Why |
|---|---|---|---|---|
| `OrderProjector` | `OrderPlaced`, `OrderShipped` | `orders` table | Sync | Needed within the same request |

## Reactors

| Reactor | Handles | Side effect |
|---|---|---|
| `SendOrderConfirmationReactor` | `OrderPlaced` | Sends confirmation email to customer |

## Invariants enforced

- <e.g., Cannot cancel a shipped order>
- <e.g., Line items must sum to > 0>

## Explicitly out of scope

- <e.g., Data migration from existing tables (greenfield)>
- <e.g., Snapshots (expected event count per aggregate < 100)>
- <e.g., Custom stored-event repository>

## Anti-patterns avoided

- <e.g., No `OrderUpdated` event — every state change has a domain-specific event>
- <e.g., No cross-aggregate invariants — shipping is a separate concern>
```
