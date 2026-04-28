<?php

namespace App\Domain\Orders\Commands;

final readonly class ShipOrderCommand
{
    public function __construct(
        public string $orderId,
        public string $shippedAt,
    ) {}
}
