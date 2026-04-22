# Design Heuristics for Event-Sourced Domains

Read this file when designing an ADR (Gate 1) or reviewing generated code. These heuristics help you make good design decisions and catch common mistakes.

## Event naming

Events are facts that already happened. Name them accordingly:

- **Past-tense verb phrases**: `OrderShipped`, `LineItemAdded`, `PaymentReceived`
- **Domain-meaningful**: The name should tell a domain expert what happened without looking at the payload. `OrderUpdated` or `StatusChanged` are red flags — they hide what actually happened.
- **Carry the delta, not a snapshot**: An `AddressChanged` event should contain the new address, not a copy of the entire order. This keeps events small, composable, and useful for projections that only care about specific changes.

**Good examples:**
| Instead of | Use |
|---|---|
| `OrderUpdated` | `OrderShipped`, `LineItemAdded`, `OrderCancelled` |
| `StatusChanged` | `PaymentReceived`, `RefundIssued` |
| `DataModified` | `CustomerEmailUpdated`, `ShippingAddressChanged` |

## Aggregate boundaries

An aggregate is a **consistency boundary** — a cluster of objects that must change atomically to maintain invariants.

- **Prefer small aggregates.** A large aggregate accumulates many events and becomes hard to reason about, test, and replay. If two pieces of state don't need to change in the same transaction, they're likely two separate aggregates.
- **Identity matters.** Each aggregate instance has a unique ID (UUID). Ask: "what is the natural identity of this thing?" An order has an order ID. A customer has a customer ID. If you find yourself compositing IDs, you might be forcing unrelated concerns into one aggregate.
- **Cross-aggregate invariants** are a design smell. If placing an order needs to check the customer's credit limit, don't merge `OrderAggregate` and `CustomerAggregate`. Instead, use a process manager (a reactor that issues commands) or check the read model before dispatching the command.

## Commands vs events

| | Commands | Events |
|---|---|---|
| Tense | Imperative, present: `PlaceOrder` | Past: `OrderPlaced` |
| Can fail? | Yes — validation, invariant violation | No — they're recorded facts |
| Cardinality | One command produces zero or more events | An event is produced by exactly one aggregate method |

A command handler validates input and calls the aggregate method. The aggregate method enforces invariants and records events (or throws). This separation keeps validation logic (handler) apart from domain logic (aggregate).

## Projectors

Projectors translate events into read models. They should be boring — no business logic, just data transformation.

- **Read models extend `Projection`.** Read models must extend `Spatie\EventSourcing\Projections\Projection`, not plain Eloquent `Model`. This makes them read-only by default, preventing accidental writes outside projectors.
- **Use the `writeable()` pattern.** Inside projectors, create with `(new Model([...]))->writeable()->save()` and update with `$model = Model::uuid($uuid); $model->field = ...; $model->writeable()->save()`. Do not use `updateOrCreate` or direct Eloquent saves — the `writeable()` gate is what makes projections safe to replay.
- **Idempotent by default.** A projector might replay events during a rebuild, and it shouldn't break. Check for existing records before creating, or use try/catch around unique constraint violations.
- **Sync is the default.** Most read models need to be consistent within the same request. Only use queued projectors for: heavy aggregations, external API calls, or read models the user won't see immediately.
- **No side effects.** A projector should never send emails, make HTTP calls, or dispatch commands. That's what reactors are for.

## Reactors

Reactors handle side effects — things that happen in the outside world in response to domain events.

- **One reactor, one side effect.** `SendOrderConfirmationReactor` sends an email. `NotifyWarehouseReactor` calls an API. Don't bundle multiple side effects into one reactor — it makes them harder to test, retry, and reason about.
- **Reactors can dispatch commands** (this is how process managers work), but declare this in the ADR. Process managers are powerful but add complexity — they should be a conscious design decision, not something that sneaks in during implementation.
- **Not guaranteed idempotent.** Unlike projectors, reactors typically perform non-idempotent operations (sending an email twice is not the same as sending it once). Spatie's reactor infrastructure handles this, but be aware of edge cases in deployment and replay scenarios.

## Anti-patterns to refuse

When designing or reviewing, actively reject these patterns:

1. **Anemic events** — `ThingUpdated` with a payload of field diffs. Every state change deserves a domain-specific event name.
2. **Projectors making HTTP calls** — Projectors are for building read models. Side effects belong in reactors.
3. **God aggregates** — One aggregate for the entire domain. If it holds unrelated state, split it.
4. **Bypassing the aggregate** — Commands that call `recordThat()` / `persist()` directly without going through an aggregate method. The aggregate is the consistency boundary; skipping it skips invariant checks.
5. **Reactors re-reading the aggregate** — The event payload should contain everything the reactor needs. If a reactor needs to load the aggregate to get data, the event is missing information.
