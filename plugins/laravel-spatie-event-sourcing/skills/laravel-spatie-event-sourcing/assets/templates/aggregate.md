# Aggregate root template

Starting point for code generation. Copy the code block and replace each `{{ placeholder }}`.

```php
<?php

namespace App\Domain\{{ context }}\Aggregates;

use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class {{ name }}Aggregate extends AggregateRoot
{
    // TODO: Add internal state properties

    // TODO: Add command methods that call recordThat()

    // TODO: Add apply methods that update internal state
}
```
