<?php

namespace App\Domain\Orders\Projectors;

use App\Domain\Orders\Events\LineItemAdded;
use App\Domain\Orders\Events\OrderCancelled;
use App\Domain\Orders\Events\OrderPlaced;
use App\Domain\Orders\Events\OrderShipped;
use App\Domain\Orders\ReadModels\Order;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class OrderProjector extends Projector
{
    public function onOrderPlaced(OrderPlaced $event, string $aggregateUuid): void
    {
        Order::updateOrCreate(
            ['uuid' => $aggregateUuid],
            [
                'customer_id' => $event->customerId,
                'customer_email' => $event->customerEmail,
                'status' => 'placed',
                'line_items' => [],
                'total_in_cents' => 0,
            ]
        );
    }

    public function onLineItemAdded(LineItemAdded $event, string $aggregateUuid): void
    {
        $order = Order::where('uuid', $aggregateUuid)->first();

        $lineItems = $order->line_items ?? [];
        $lineItems[] = [
            'sku' => $event->sku,
            'quantity' => $event->quantity,
            'price_in_cents' => $event->priceInCents,
            'description' => $event->description,
        ];

        $lineTotal = $event->quantity * $event->priceInCents;

        $order->update([
            'line_items' => $lineItems,
            'total_in_cents' => $order->total_in_cents + $lineTotal,
        ]);
    }

    public function onOrderShipped(OrderShipped $event, string $aggregateUuid): void
    {
        Order::where('uuid', $aggregateUuid)->update([
            'status' => 'shipped',
            'shipped_at' => $event->shippedAt,
        ]);
    }

    public function onOrderCancelled(OrderCancelled $event, string $aggregateUuid): void
    {
        Order::where('uuid', $aggregateUuid)->update([
            'status' => 'cancelled',
            'cancelled_at' => $event->cancelledAt,
            'cancellation_reason' => $event->reason,
        ]);
    }
}
