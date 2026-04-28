<?php

namespace App\Domain\Orders\Commands;

final readonly class AddLineItemCommand
{
    public function __construct(
        public string $orderId,
        public string $sku,
        public int $quantity,
        public int $priceInCents,
        public string $description,
    ) {}
}
