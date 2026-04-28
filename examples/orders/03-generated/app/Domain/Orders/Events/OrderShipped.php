<?php

namespace App\Domain\Orders\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class OrderShipped extends ShouldBeStored
{
    public function __construct(
        public readonly string $shippedAt,
    ) {}
}
