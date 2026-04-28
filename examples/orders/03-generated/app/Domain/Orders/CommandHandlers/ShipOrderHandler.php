<?php

namespace App\Domain\Orders\CommandHandlers;

use App\Domain\Orders\Aggregates\OrderAggregate;
use App\Domain\Orders\Commands\ShipOrderCommand;

final class ShipOrderHandler
{
    public function __invoke(ShipOrderCommand $command): void
    {
        OrderAggregate::retrieve($command->orderId)
            ->ship(shippedAt: $command->shippedAt)
            ->persist();
    }
}
