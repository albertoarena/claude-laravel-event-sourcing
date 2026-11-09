# Command (bus-dispatched) template

Starting point for code generation. Copy the code block and replace each `{{ placeholder }}`.

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
