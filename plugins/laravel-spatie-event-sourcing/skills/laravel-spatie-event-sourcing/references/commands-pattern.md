# Command + Handler Patterns

This skill supports two command dispatch styles. The user chooses during project bootstrap, and the choice is stored in `.claude/event-sourcing.md`.

## Style 1: Plain handler classes (default)

Commands are simple DTOs. Handlers are classes with an `__invoke` method that takes the command, loads the aggregate, and calls the appropriate method.

### Command DTO

```php
<?php

namespace App\Domain\Orders\Commands;

final readonly class PlaceOrderCommand
{
    public function __construct(
        public string $orderId,
        public string $customerId,
        public array $items,
    ) {}
}
```

### Handler

```php
<?php

namespace App\Domain\Orders\CommandHandlers;

use App\Domain\Orders\Aggregates\OrderAggregate;
use App\Domain\Orders\Commands\PlaceOrderCommand;

final class PlaceOrderHandler
{
    public function __invoke(PlaceOrderCommand $command): void
    {
        OrderAggregate::retrieve($command->orderId)
            ->place(
                customerId: $command->customerId,
                items: $command->items,
            )
            ->persist();
    }
}
```

### Usage (from a controller, job, or other entry point)

```php
$handler = new PlaceOrderHandler();
$handler(new PlaceOrderCommand(
    orderId: Str::uuid(),
    customerId: $request->customer_id,
    items: $request->items,
));
```

## Style 2: Laravel command bus (`Bus::dispatch`)

Commands implement `ShouldQueue` or are dispatched synchronously. Handlers are resolved from the container.

### Command DTO

```php
<?php

namespace App\Domain\Orders\Commands;

use Illuminate\Foundation\Bus\Dispatchable;

final readonly class PlaceOrderCommand
{
    use Dispatchable;

    public function __construct(
        public string $orderId,
        public string $customerId,
        public array $items,
    ) {}
}
```

### Handler

```php
<?php

namespace App\Domain\Orders\CommandHandlers;

use App\Domain\Orders\Aggregates\OrderAggregate;
use App\Domain\Orders\Commands\PlaceOrderCommand;

final class PlaceOrderHandler
{
    public function handle(PlaceOrderCommand $command): void
    {
        OrderAggregate::retrieve($command->orderId)
            ->place(
                customerId: $command->customerId,
                items: $command->items,
            )
            ->persist();
    }
}
```

### Registration

Map commands to handlers in a service provider:

```php
// app/Providers/EventSourcingServiceProvider.php
use Illuminate\Support\Facades\Bus;

public function boot(): void
{
    Bus::map([
        PlaceOrderCommand::class => PlaceOrderHandler::class,
    ]);
}
```

### Usage

```php
Bus::dispatch(new PlaceOrderCommand(
    orderId: Str::uuid(),
    customerId: $request->customer_id,
    items: $request->items,
));
```

## Which style to choose?

- **Plain handlers** are simpler, easier to test, and have no framework coupling. Good default for most projects.
- **Bus dispatch** is useful when you want Laravel's middleware pipeline (e.g., `ShouldQueue`), need to dispatch commands from jobs or events, or your team already uses the command bus pattern elsewhere.

Both styles are functionally equivalent for event sourcing purposes. The aggregate doesn't know or care how it was called.
