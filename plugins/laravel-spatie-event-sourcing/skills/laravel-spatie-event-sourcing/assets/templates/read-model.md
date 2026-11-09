# Read model template

Starting point for code generation. Copy the code block and replace each `{{ placeholder }}`.

```php
<?php

namespace App\Domain\{{ context }}\ReadModels;

use Spatie\EventSourcing\Projections\Projection;

class {{ name }} extends Projection
{
    protected $guarded = [];

    protected $table = '{{ table }}';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';
}
```
