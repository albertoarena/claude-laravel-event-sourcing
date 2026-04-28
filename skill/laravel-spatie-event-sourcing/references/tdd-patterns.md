# TDD Patterns for Event-Sourced Domains

Write tests BEFORE implementation (Gate 2, step 2). These patterns show how to test aggregates, projectors, and reactors with both Pest and PHPUnit.

## Aggregate tests

Aggregate tests follow a **given-when-then** pattern using Spatie's `::fake()` helper:
- **given**: Past events that set up the aggregate's state
- **when**: The command/method being tested
- **then**: Assert which events were recorded (or that an exception was thrown)

### Pest

```php
<?php

use App\Domain\Orders\Aggregates\OrderAggregate;
use App\Domain\Orders\Events\OrderPlaced;
use App\Domain\Orders\Events\OrderShipped;
use App\Domain\Orders\Events\OrderCancelled;
use App\Domain\Orders\Exceptions\CannotCancelShippedOrder;

it('records OrderPlaced when placing an order', function () {
    $uuid = fake()->uuid();

    OrderAggregate::fake($uuid)
        ->when(fn (OrderAggregate $a) => $a->place(
            customerId: 'c-1',
            items: [['sku' => 'WIDGET-1', 'qty' => 2, 'price' => 1999]]
        ))
        ->assertRecorded([
            new OrderPlaced(
                customerId: 'c-1',
                items: [['sku' => 'WIDGET-1', 'qty' => 2, 'price' => 1999]]
            ),
        ]);
});

it('refuses to cancel a shipped order', function () {
    $uuid = fake()->uuid();

    OrderAggregate::fake($uuid)
        ->given([
            new OrderPlaced(customerId: 'c-1', items: [['sku' => 'WIDGET-1', 'qty' => 1, 'price' => 999]]),
            new OrderShipped(),
        ])
        ->when(fn (OrderAggregate $a) => $a->cancel())
        ->assertNothingRecorded()
        ->assertExceptionThrown(CannotCancelShippedOrder::class);
});
```

### PHPUnit

```php
<?php

namespace Tests\Feature\Orders;

use App\Domain\Orders\Aggregates\OrderAggregate;
use App\Domain\Orders\Events\OrderPlaced;
use Tests\TestCase;

class OrderAggregateTest extends TestCase
{
    public function test_it_records_order_placed_when_placing_an_order(): void
    {
        $uuid = fake()->uuid();

        OrderAggregate::fake($uuid)
            ->when(fn (OrderAggregate $a) => $a->place(
                customerId: 'c-1',
                items: [['sku' => 'WIDGET-1', 'qty' => 2, 'price' => 1999]]
            ))
            ->assertRecorded([
                new OrderPlaced(
                    customerId: 'c-1',
                    items: [['sku' => 'WIDGET-1', 'qty' => 2, 'price' => 1999]]
                ),
            ]);
    }
}
```

## Projector tests

Projector tests instantiate the projector directly and call its handler methods with crafted events. Assert the read model was written correctly.

Note: Read models extend `Spatie\EventSourcing\Projections\Projection` (not plain Eloquent `Model`). Projectors use the `writeable()` pattern — see `spatie-api-cheatsheet.md` for details.

### Pest

```php
<?php

use App\Domain\Orders\Events\OrderPlaced;
use App\Domain\Orders\Projectors\OrderProjector;
use App\Domain\Orders\ReadModels\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates an order row on OrderPlaced', function () {
    $event = new OrderPlaced(
        customerId: 'c-1',
        items: [['sku' => 'WIDGET-1', 'qty' => 2, 'price' => 1999]]
    );

    (new OrderProjector())->onOrderPlaced($event, aggregateUuid: 'o-1');

    $order = Order::uuid('o-1');
    expect($order)->not->toBeNull()
        ->and($order->customer_id)->toBe('c-1');
});

it('is idempotent — handles the same event twice', function () {
    $event = new OrderPlaced(
        customerId: 'c-1',
        items: [['sku' => 'WIDGET-1', 'qty' => 1, 'price' => 999]]
    );

    $projector = new OrderProjector();
    $projector->onOrderPlaced($event, aggregateUuid: 'o-1');
    $projector->onOrderPlaced($event, aggregateUuid: 'o-1');

    expect(Order::where('uuid', 'o-1')->count())->toBe(1);
});
```

### PHPUnit

```php
<?php

namespace Tests\Feature\Orders;

use App\Domain\Orders\Events\OrderPlaced;
use App\Domain\Orders\Projectors\OrderProjector;
use App\Domain\Orders\ReadModels\Order;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderProjectorTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_order_row_on_order_placed(): void
    {
        $event = new OrderPlaced(
            customerId: 'c-1',
            items: [['sku' => 'WIDGET-1', 'qty' => 2, 'price' => 1999]]
        );

        (new OrderProjector())->onOrderPlaced($event, aggregateUuid: 'o-1');

        $order = Order::uuid('o-1');
        $this->assertNotNull($order);
        $this->assertEquals('c-1', $order->customer_id);
    }
}
```

## Reactor tests

Reactor tests fake the side-effect layer (Mail, Http, Queue, etc.) and verify the reactor triggers it when handling an event.

### Pest

```php
<?php

use App\Domain\Orders\Events\OrderPlaced;
use App\Domain\Orders\Reactors\SendOrderConfirmationReactor;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Mail;

it('sends confirmation email on OrderPlaced', function () {
    Mail::fake();

    $event = new OrderPlaced(
        customerId: 'c-1',
        items: [['sku' => 'WIDGET-1', 'qty' => 1, 'price' => 999]]
    );

    (new SendOrderConfirmationReactor())->onOrderPlaced($event);

    Mail::assertSent(OrderConfirmationMail::class);
});
```

### PHPUnit

```php
<?php

namespace Tests\Feature\Orders;

use App\Domain\Orders\Events\OrderPlaced;
use App\Domain\Orders\Reactors\SendOrderConfirmationReactor;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendOrderConfirmationReactorTest extends TestCase
{
    public function test_it_sends_confirmation_email_on_order_placed(): void
    {
        Mail::fake();

        $event = new OrderPlaced(
            customerId: 'c-1',
            items: [['sku' => 'WIDGET-1', 'qty' => 1, 'price' => 999]]
        );

        (new SendOrderConfirmationReactor())->onOrderPlaced($event);

        Mail::assertSent(OrderConfirmationMail::class);
    }
}
```

## Key testing principles

- **Tests come first.** Write the test, see it fail (or not compile), then write the implementation.
- **Use `::fake()` for aggregates.** It handles event sourcing plumbing so you can focus on behavior.
- **Projector tests need `RefreshDatabase`.** They write to the database. Aggregate tests typically don't (they use the fake).
- **Reactor tests fake the side effect**, not the event sourcing infrastructure.
- **One behavior per test.** Each test should verify one thing — one command producing the right events, one projector handling one event type, one reactor triggering one side effect.
