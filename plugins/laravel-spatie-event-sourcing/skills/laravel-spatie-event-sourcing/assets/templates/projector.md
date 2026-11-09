# Projector template

Starting point for code generation. Copy the code block and replace each `{{ placeholder }}`.

```php
<?php

namespace App\Domain\{{ context }}\Projectors;

use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class {{ name }}Projector extends Projector
{
    // TODO: Add on<EventName> methods that build the read model
    // Create: (new ReadModel([...]))->writeable()->save()
    // Update: $model = ReadModel::uuid($uuid); $model->field = ...; $model->writeable()->save()
}
```
