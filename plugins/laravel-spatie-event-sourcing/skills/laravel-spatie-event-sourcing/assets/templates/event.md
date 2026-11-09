# Event template

Starting point for code generation. Copy the code block and replace each `{{ placeholder }}`.

```php
<?php

namespace App\Domain\{{ context }}\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class {{ name }} extends ShouldBeStored
{
    public function __construct(
        // TODO: Add readonly payload properties
    ) {}
}
```
