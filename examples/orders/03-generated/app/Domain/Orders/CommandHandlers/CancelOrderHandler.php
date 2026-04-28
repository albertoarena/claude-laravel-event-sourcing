<?php

namespace App\Domain\Orders\CommandHandlers;

use App\Domain\Orders\Aggregates\OrderAggregate;
use App\Domain\Orders\Commands\CancelOrderCommand;

final class CancelOrderHandler
{
    public function __invoke(CancelOrderCommand $command): void
    {
        OrderAggregate::retrieve($command->orderId)
            ->cancel(
                cancelledAt: $command->cancelledAt,
                reason: $command->reason,
            )
            ->persist();
    }
}
