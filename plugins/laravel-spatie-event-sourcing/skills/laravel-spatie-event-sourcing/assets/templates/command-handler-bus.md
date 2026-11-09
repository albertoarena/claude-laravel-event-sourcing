# Command handler (bus-dispatched) template

Starting point for code generation. Copy the code block and replace each `{{ placeholder }}`.

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
