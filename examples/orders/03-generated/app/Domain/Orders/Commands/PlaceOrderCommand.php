<?php

namespace App\Domain\Orders\Commands;

final readonly class PlaceOrderCommand
{
    public function __construct(
        public string $orderId,
        public string $customerId,
        public string $customerEmail,
    ) {}
}
