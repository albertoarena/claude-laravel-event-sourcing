<?php

namespace App\Domain\Orders\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LineItemAdded extends ShouldBeStored
{
    public function __construct(
        public readonly string $sku,
        public readonly int $quantity,
        public readonly int $priceInCents,
        public readonly string $description,
    ) {}
}
