<?php

namespace App\Domain\Orders\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class OrderCancelled extends ShouldBeStored
{
    public function __construct(
        public readonly string $cancelledAt,
        public readonly string $reason,
    ) {}
}
