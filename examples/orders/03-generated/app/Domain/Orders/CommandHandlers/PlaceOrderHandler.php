<?php

namespace App\Domain\Orders\CommandHandlers;

use App\Domain\Orders\Aggregates\OrderAggregate;
use App\Domain\Orders\Commands\PlaceOrderCommand;

final class PlaceOrderHandler
{
    public function __invoke(PlaceOrderCommand $command): void
    {
        OrderAggregate::retrieve($command->orderId)
            ->place(
                customerId: $command->customerId,
                customerEmail: $command->customerEmail,
            )
            ->persist();
    }
}
