# Command template

Starting point for code generation. Copy the code block and replace each `{{ placeholder }}`.

```php
<?php

namespace App\Domain\{{ context }}\Commands;

final readonly class {{ name }}Command
{
    public function __construct(
        public string ${{ aggregateId }},
        // TODO: Add command properties
    ) {}
}
```
