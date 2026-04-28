# Sub-plan: Orders Worked Example

**Parent:** [backlog.md](./backlog.md) — H1
**Status:** DONE

## Goal

Create a complete worked example that demonstrates every skill capability end-to-end: one aggregate, four events, two projectors (sync + queued), one reactor. Serves as both documentation and regression test.

## Prerequisites

- H2 (verify-setup.sh) — fixed, so the example can reference accurate bootstrap
- H3 (missing stubs) — complete, so the example uses all stub types
- M1, M2 — fixed, so generated test patterns and read models are correct

## Steps

### 1. Create the user prompt

**File:** `examples/orders/01-user-prompt.md`

Use the exact prompt from `skill-spec.md` section 9:
> "I want to add order management to my Laravel app using Spatie event sourcing. Orders can be placed, have line items added, shipped, and cancelled — but only if not already shipped. I need a standard orders read model and daily revenue statistics. Ship confirmation should email the customer."

### 2. Write the ADR (Gate 1 output)

**File:** `examples/orders/02-adr.md`

Use the ADR template from `references/adr-template.md`. Must cover:
- `OrderAggregate` with consistency boundary and UUID strategy
- Commands: `PlaceOrder`, `AddLineItem`, `ShipOrder`, `CancelOrder`
- Events: `OrderPlaced`, `LineItemAdded`, `OrderShipped`, `OrderCancelled`
- Projectors: `OrderProjector` (sync), `OrderStatisticsProjector` (queued)
- Reactor: `SendOrderConfirmationReactor`
- Invariants: cannot cancel a shipped order, line items must sum to > 0
- Anti-patterns avoided

### 3. Generate the code (Gate 2 output)

**Directory:** `examples/orders/03-generated/`

```
03-generated/
├── app/Domain/Orders/
│   ├── Aggregates/OrderAggregate.php
│   ├── Commands/
│   │   ├── PlaceOrderCommand.php
│   │   ├── AddLineItemCommand.php
│   │   ├── ShipOrderCommand.php
│   │   └── CancelOrderCommand.php
│   ├── CommandHandlers/
│   │   ├── PlaceOrderHandler.php
│   │   ├── AddLineItemHandler.php
│   │   ├── ShipOrderHandler.php
│   │   └── CancelOrderHandler.php
│   ├── Events/
│   │   ├── OrderPlaced.php
│   │   ├── LineItemAdded.php
│   │   ├── OrderShipped.php
│   │   └── OrderCancelled.php
│   ├── Projectors/
│   │   ├── OrderProjector.php
│   │   └── OrderStatisticsProjector.php
│   ├── Reactors/
│   │   └── SendOrderConfirmationReactor.php
│   └── ReadModels/
│       ├── Order.php
│       └── OrderStatistic.php
├── tests/Feature/Orders/
│   ├── OrderAggregateTest.php
│   ├── OrderProjectorTest.php
│   ├── OrderStatisticsProjectorTest.php
│   └── SendOrderConfirmationReactorTest.php
└── database/migrations/
    ├── xxxx_xx_xx_create_orders_table.php
    └── xxxx_xx_xx_create_order_statistics_table.php
```

Use plain handler style (default). Use Pest for tests.

### 4. Capture test output

**File:** `examples/orders/04-test-output.txt`

Run the test suite against a fresh Laravel 11 + spatie v7 project and capture passing output.

**Approach suggestion:** Rather than manually crafting test output, consider setting up a temporary Laravel project in CI (or locally via a script) that copies the generated code in, runs `vendor/bin/pest`, and captures the output. This ensures the example stays honest.

### 5. Write the walkthrough narrative

**File:** `examples/orders/README.md`

Short document (~50 lines) that walks through:
- What the example demonstrates
- How to read the ADR
- What each generated file does
- How to run the tests yourself

## Validation

- [ ] All generated code compiles (no syntax errors)
- [ ] Tests pass on Laravel 11 + spatie v7 + Pest
- [ ] ADR follows the template exactly
- [ ] Every artifact type from the directory conventions is represented
- [ ] Code matches the patterns in reference files (writeable(), ::fake(), etc.)

## Alternative approach (recommended)

Instead of hand-writing all the example code, **use the skill itself** to generate it:

1. Set up a fresh Laravel 11 project with spatie v7
2. Install the skill
3. Feed it the user prompt from step 1
4. Approve the ADR it produces
5. Let it generate the code and run tests
6. Copy the outputs into `examples/orders/`

This approach ensures the example is a true representation of what the skill produces, and acts as an end-to-end smoke test. Any issues found during this process become additional backlog items.
