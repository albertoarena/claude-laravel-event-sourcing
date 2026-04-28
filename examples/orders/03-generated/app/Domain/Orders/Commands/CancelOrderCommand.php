<?php

namespace App\Domain\Orders\Commands;

final readonly class CancelOrderCommand
{
    public function __construct(
        public string $orderId,
        public string $cancelledAt,
        public string $reason,
    ) {}
}
