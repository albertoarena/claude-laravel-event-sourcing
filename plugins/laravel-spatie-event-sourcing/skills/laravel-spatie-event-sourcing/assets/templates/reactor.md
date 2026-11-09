# Reactor template

Starting point for code generation. Copy the code block and replace each `{{ placeholder }}`.

```php
<?php

namespace App\Domain\{{ context }}\Reactors;

use Spatie\EventSourcing\EventHandlers\EventHandler;

class {{ name }}Reactor extends EventHandler
{
    // TODO: Add on<EventName> methods that trigger side effects
}
```
