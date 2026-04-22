# Spatie Laravel Event Sourcing — API Cheatsheet

Quick reference for the key classes and methods in `spatie/laravel-event-sourcing` v7.

## Table of contents

1. [AggregateRoot](#aggregateroot)
2. [ShouldBeStored (Events)](#shouldbestored-events)
3. [Projector](#projector)
4. [EventHandler / Reactor](#eventhandler--reactor)
5. [Testing with ::fake()](#testing-with-fake)
6. [Configuration](#configuration)

## AggregateRoot

```php
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class OrderAggregate extends AggregateRoot
{
    // Internal state — rebuilt from events
    protected bool $isShipped = false;

    // Command method — validates, records events
    public function place(string $customerId, array $items): self
    {
        $this->recordThat(new OrderPlaced(
            customerId: $customerId,
            items: $items,
        ));

        return $this;
    }

    // Apply method — updates internal state (called during replay AND recording)
    protected function applyOrderPlaced(OrderPlaced $event): void
    {
        // Update internal state used by invariant checks
    }

    protected function applyOrderShipped(OrderShipped $event): void
    {
        $this->isShipped = true;
    }

    public function cancel(): self
    {
        if ($this->isShipped) {
            throw new CannotCancelShippedOrder();
        }

        $this->recordThat(new OrderCancelled());

        return $this;
    }
}
```

Key methods:
- `recordThat(ShouldBeStored $event)` — Records an event (does not persist yet)
- `persist()` — Persists all recorded events to the store and dispatches them
- `::retrieve(string $uuid)` — Loads an aggregate by replaying its events
- `::fake(string $uuid)` — Creates a testable aggregate instance

## ShouldBeStored (Events)

```php
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class OrderPlaced extends ShouldBeStored
{
    public function __construct(
        public readonly string $customerId,
        public readonly array $items,
    ) {}
}
```

Events are simple value objects. They implement `ShouldBeStored` which handles serialization. Use `readonly` properties and constructor promotion for clean, immutable events.

## Projection (Read Model)

Read models must extend `Spatie\EventSourcing\Projections\Projection`, not plain Eloquent `Model`. Projection models are read-only by default — use `writeable()` to persist changes inside projectors.

```php
use Spatie\EventSourcing\Projections\Projection;

class Order extends Projection
{
    protected $guarded = [];

    // Projections are read-only by default.
    // Use writeable() in projectors to save changes.
}
```

## Projector

Projectors use the `writeable()` pattern to create and update projections:

```php
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class OrderProjector extends Projector
{
    public function onOrderPlaced(OrderPlaced $event, string $aggregateUuid): void
    {
        // Create: instantiate with attributes, then writeable()->save()
        (new Order([
            'uuid' => $aggregateUuid,
            'customer_id' => $event->customerId,
            'items' => $event->items,
            'status' => 'placed',
        ]))->writeable()->save();
    }

    public function onOrderShipped(OrderShipped $event, string $aggregateUuid): void
    {
        // Update: find by uuid, modify, then writeable()->save()
        $order = Order::uuid($aggregateUuid);
        $order->status = 'shipped';
        $order->writeable()->save();
    }
}
```

Handler method signature: `on<EventClassName>(EventClass $event, string $aggregateUuid)`

For queued projectors, add the `Illuminate\Contracts\Queue\ShouldQueue` interface:

```php
class OrderStatisticsProjector extends Projector implements ShouldQueue
{
    // ...
}
```

## EventHandler / Reactor

```php
use Spatie\EventSourcing\EventHandlers\EventHandler;

class SendOrderConfirmationReactor extends EventHandler
{
    public function onOrderPlaced(OrderPlaced $event, string $aggregateUuid): void
    {
        Mail::to($this->getCustomerEmail($event->customerId))
            ->send(new OrderConfirmationMail($event));
    }
}
```

Reactors extend `EventHandler` (not `Projector`). Same method naming convention.

## Testing with ::fake()

```php
// Given-when-then pattern
OrderAggregate::fake($uuid)
    ->given([new OrderPlaced(...)])          // Set up past events
    ->when(fn (OrderAggregate $a) => $a->ship())  // Execute command
    ->assertRecorded([new OrderShipped()])   // Assert events recorded
    ->assertNothingRecorded()               // Assert NO events recorded
    ->assertExceptionThrown(SomeException::class); // Assert exception

// The aggregate uuid is available
OrderAggregate::fake()  // Auto-generates UUID if not provided
```

## Configuration

`config/event-sourcing.php` key settings:

```php
return [
    // Register projectors
    'projectors' => [
        App\Domain\Orders\Projectors\OrderProjector::class,
        App\Domain\Orders\Projectors\OrderStatisticsProjector::class,
    ],

    // Register reactors
    'reactors' => [
        App\Domain\Orders\Reactors\SendOrderConfirmationReactor::class,
    ],

    // Stored event model (usually leave as default)
    'stored_event_model' => Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent::class,

    // Default serializer
    'event_serializer' => Spatie\EventSourcing\EventSerializers\JsonEventSerializer::class,
];
```
