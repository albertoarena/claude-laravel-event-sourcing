# Sub-plan: Missing Stub Templates

**Parent:** [backlog.md](./backlog.md) — H3
**Status:** TODO

## Goal

Add stub templates for every artifact type the skill generates, so code generation has consistent scaffolding.

## Current stubs

| Stub | Exists |
|---|---|
| `aggregate.stub` | Yes |
| `aggregate.pest.stub` | Yes |
| `aggregate.phpunit.stub` | Yes |
| `command.stub` | Yes |
| `command-handler.stub` | Yes (plain style only) |
| `event.stub` | Yes |
| `projector.stub` | Yes |
| `reactor.stub` | Yes |
| `read-model.stub` | Yes |

## Missing stubs

### 1. `migration.stub`

Read model table migration. Referenced in directory conventions but has no template.

```
{{ timestamp }}_create_{{ table }}_table.php
```

Should include:
- UUID column (primary key)
- `timestamps()`
- Placeholder columns with `// TODO:` for domain-specific fields

**Note:** Consider whether a migration stub is truly useful, or whether Claude is better off generating migrations from scratch based on the ADR's read model definition. Migrations are highly domain-specific — a stub with `// TODO: add columns` adds little value. **Recommendation:** Skip the stub; instead, add a brief note in SKILL.md Gate 2 instructions about migration structure (UUID primary key, no auto-increment, timestamps). This keeps the stub directory focused on files where the scaffold is genuinely reusable.

### 2. `command-bus.stub`

Command DTO for the bus dispatch style — adds `Dispatchable` trait.

```php
<?php

namespace App\Domain\{{ context }}\Commands;

use Illuminate\Foundation\Bus\Dispatchable;

final readonly class {{ name }}Command
{
    use Dispatchable;

    public function __construct(
        public string ${{ aggregateId }},
        // TODO: Add command properties
    ) {}
}
```

### 3. `command-handler-bus.stub`

Handler for bus dispatch style — uses `handle()` instead of `__invoke()`.

```php
<?php

namespace App\Domain\{{ context }}\CommandHandlers;

use App\Domain\{{ context }}\Aggregates\{{ aggregate }}Aggregate;
use App\Domain\{{ context }}\Commands\{{ name }}Command;

final class {{ name }}Handler
{
    public function handle({{ name }}Command $command): void
    {
        {{ aggregate }}Aggregate::retrieve($command->{{ aggregateId }})
            // TODO: Call aggregate method
            ->persist();
    }
}
```

### 4. `projector.pest.stub`

```php
<?php

use App\Domain\{{ context }}\Events\{{ eventName }};
use App\Domain\{{ context }}\Projectors\{{ name }}Projector;
use App\Domain\{{ context }}\ReadModels\{{ readModel }};
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates {{ readModel }} on {{ eventName }}', function () {
    $event = new {{ eventName }}(
        // TODO: Add event constructor arguments
    );

    (new {{ name }}Projector())->on{{ eventName }}($event, aggregateUuid: fake()->uuid());

    expect({{ readModel }}::uuid($event->aggregateUuid ?? fake()->uuid()))->not->toBeNull();
});

it('is idempotent — handles the same event twice', function () {
    $uuid = fake()->uuid();
    $event = new {{ eventName }}(
        // TODO: Add event constructor arguments
    );

    $projector = new {{ name }}Projector();
    $projector->on{{ eventName }}($event, aggregateUuid: $uuid);
    $projector->on{{ eventName }}($event, aggregateUuid: $uuid);

    expect({{ readModel }}::where('uuid', $uuid)->count())->toBe(1);
});
```

### 5. `projector.phpunit.stub`

```php
<?php

namespace Tests\Feature\{{ context }};

use App\Domain\{{ context }}\Events\{{ eventName }};
use App\Domain\{{ context }}\Projectors\{{ name }}Projector;
use App\Domain\{{ context }}\ReadModels\{{ readModel }};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class {{ name }}ProjectorTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_{{ snake_readModel }}_on_{{ snake_event }}(): void
    {
        $event = new {{ eventName }}(
            // TODO: Add event constructor arguments
        );

        (new {{ name }}Projector())->on{{ eventName }}($event, aggregateUuid: fake()->uuid());

        $this->assertNotNull({{ readModel }}::uuid(fake()->uuid()));
    }
}
```

### 6. `reactor.pest.stub`

```php
<?php

use App\Domain\{{ context }}\Events\{{ eventName }};
use App\Domain\{{ context }}\Reactors\{{ name }}Reactor;

// TODO: Import the side-effect facade (Mail, Http, Bus, etc.)

it('{{ sideEffect }} on {{ eventName }}', function () {
    // TODO: Fake the side-effect facade
    // e.g. Mail::fake();

    $event = new {{ eventName }}(
        // TODO: Add event constructor arguments
    );

    (new {{ name }}Reactor())->on{{ eventName }}($event);

    // TODO: Assert the side effect was triggered
    // e.g. Mail::assertSent(SomeMail::class);
});
```

### 7. `reactor.phpunit.stub`

```php
<?php

namespace Tests\Feature\{{ context }};

use App\Domain\{{ context }}\Events\{{ eventName }};
use App\Domain\{{ context }}\Reactors\{{ name }}Reactor;
use Tests\TestCase;

// TODO: Import the side-effect facade and mailable/notification class

class {{ name }}ReactorTest extends TestCase
{
    public function test_it_{{ snake_sideEffect }}_on_{{ snake_event }}(): void
    {
        // TODO: Fake the side-effect facade
        // e.g. Mail::fake();

        $event = new {{ eventName }}(
            // TODO: Add event constructor arguments
        );

        (new {{ name }}Reactor())->on{{ eventName }}($event);

        // TODO: Assert the side effect was triggered
        // e.g. Mail::assertSent(SomeMail::class);
    }
}
```

## Execution order

1. `command-bus.stub` + `command-handler-bus.stub` (paired, bus style support)
2. `projector.pest.stub` + `projector.phpunit.stub` (paired, test coverage)
3. `reactor.pest.stub` + `reactor.phpunit.stub` (paired, test coverage)
4. `migration.stub` — only if decided against the "skip it" recommendation

## After completion

- Update SKILL.md quick reference section to mention new stubs
- Rebuild the `.skill` ZIP package
