# ADR: Order Management via Event Sourcing

**Status:** Proposed - 2026-04-20
**Scope:** Order lifecycle management -- placing orders with line items, shipping, and cancellation with business rule enforcement.

## Context

Event sourcing is a strong fit for order management because:

- Orders go through a well-defined lifecycle (placed, items added, shipped, cancelled) where the full history of state transitions has business value.
- There are strict invariants (e.g., cannot cancel a shipped order) that benefit from aggregate-level enforcement.
- Multiple read models (order detail view, daily revenue statistics) need to be derived from the same event stream.
- Side effects (ship confirmation email) must happen reliably in response to domain events.
- Plain CRUD would lose the audit trail of order changes and make it harder to enforce lifecycle invariants consistently.

## Aggregates

### `OrderAggregate`
- **Consistency boundary:** A single order's lifecycle -- its status, line items, and the transitions between states. All changes to an order must go through this aggregate to enforce invariants.
- **Identity:** UUID passed in by the caller (typically `Str::uuid()` at the controller/handler level).

Only one aggregate is needed. Line items are part of the order's consistency boundary since they affect order totals and cannot exist independently. Shipping and cancellation are status transitions on the same order.

## Commands

| Command | Handler | Aggregate method |
|---|---|---|
| `PlaceOrderCommand` | `PlaceOrderHandler` | `OrderAggregate::place()` |
| `AddLineItemCommand` | `AddLineItemHandler` | `OrderAggregate::addLineItem()` |
| `ShipOrderCommand` | `ShipOrderHandler` | `OrderAggregate::ship()` |
| `CancelOrderCommand` | `CancelOrderHandler` | `OrderAggregate::cancel()` |

## Events

| Event | Recorded by | Payload |
|---|---|---|
| `OrderPlaced` | `OrderAggregate::place()` | customerId (string), customerEmail (string) |
| `LineItemAdded` | `OrderAggregate::addLineItem()` | sku (string), quantity (int), priceInCents (int), description (string) |
| `OrderShipped` | `OrderAggregate::ship()` | shippedAt (string, ISO 8601 datetime) |
| `OrderCancelled` | `OrderAggregate::cancel()` | cancelledAt (string, ISO 8601 datetime), reason (string) |

## Projectors

| Projector | Handles | Read model | Sync/Queued | Why |
|---|---|---|---|---|
| `OrderProjector` | `OrderPlaced`, `LineItemAdded`, `OrderShipped`, `OrderCancelled` | `orders` table (Order model) | Sync | Order status must be visible immediately after the command completes |
| `DailyRevenueProjector` | `OrderPlaced`, `LineItemAdded`, `OrderCancelled` | `daily_revenue_stats` table (DailyRevenueStat model) | Queued | Statistics are not needed within the same request; can be eventually consistent |

## Reactors

| Reactor | Handles | Side effect |
|---|---|---|
| `SendShipConfirmationReactor` | `OrderShipped` | Sends ship confirmation email to the customer |

## Invariants enforced

- Cannot cancel a shipped order (OrderAggregate checks `isShipped` state before recording `OrderCancelled`)
- Cannot ship an already-shipped order (prevents duplicate shipping)
- Cannot ship a cancelled order
- Cannot add line items to a shipped or cancelled order
- Order must be placed before any other operations (aggregate tracks `isPlaced` state)

## Explicitly out of scope

- Data migration from existing tables (greenfield implementation)
- Snapshots (expected event count per order aggregate is small, well under 100)
- Payment processing (separate bounded context)
- Inventory management (separate bounded context)
- Custom stored-event repository (default Spatie storage is sufficient)
- Order line item removal (not requested; can be added later)

## Anti-patterns avoided

- No `OrderUpdated` event -- every state change has a domain-specific event name (`OrderPlaced`, `LineItemAdded`, `OrderShipped`, `OrderCancelled`)
- No projectors with side effects -- email sending is in a reactor, not a projector
- No cross-aggregate invariants -- order management is self-contained
- No god aggregate -- order is a naturally cohesive boundary; shipping/payment would be separate if needed
- Reactor does not re-read the aggregate -- `OrderShipped` event is handled by a reactor that reads customer email from the read model (projected by the sync `OrderProjector` which runs before the reactor)
