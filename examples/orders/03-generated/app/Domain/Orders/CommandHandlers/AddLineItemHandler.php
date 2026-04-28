<?php

namespace App\Domain\Orders\CommandHandlers;

use App\Domain\Orders\Aggregates\OrderAggregate;
use App\Domain\Orders\Commands\AddLineItemCommand;

final class AddLineItemHandler
{
    public function __invoke(AddLineItemCommand $command): void
    {
        OrderAggregate::retrieve($command->orderId)
            ->addLineItem(
                sku: $command->sku,
                quantity: $command->quantity,
                priceInCents: $command->priceInCents,
                description: $command->description,
            )
            ->persist();
    }
}
