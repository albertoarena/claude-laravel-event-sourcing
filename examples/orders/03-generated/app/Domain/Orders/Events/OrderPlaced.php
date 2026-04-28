<?php

namespace App\Domain\Orders\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class OrderPlaced extends ShouldBeStored
{
    public function __construct(
        public readonly string $customerId,
        public readonly string $customerEmail,
    ) {}
}
