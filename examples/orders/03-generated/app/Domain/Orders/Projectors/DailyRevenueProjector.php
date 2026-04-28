<?php

namespace App\Domain\Orders\Projectors;

use App\Domain\Orders\Events\LineItemAdded;
use App\Domain\Orders\Events\OrderCancelled;
use App\Domain\Orders\Events\OrderPlaced;
use App\Domain\Orders\ReadModels\DailyRevenueStat;
use App\Domain\Orders\ReadModels\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class DailyRevenueProjector extends Projector implements ShouldQueue
{
    public function onOrderPlaced(OrderPlaced $event, string $aggregateUuid): void
    {
        $today = now()->toDateString();

        DailyRevenueStat::updateOrCreate(
            ['date' => $today],
            [
                'total_revenue_in_cents' => 0,
                'order_count' => 0,
                'line_item_count' => 0,
            ]
        );

        DailyRevenueStat::where('date', $today)->increment('order_count');
    }

    public function onLineItemAdded(LineItemAdded $event, string $aggregateUuid): void
    {
        $today = now()->toDateString();

        $lineTotal = $event->quantity * $event->priceInCents;

        DailyRevenueStat::updateOrCreate(
            ['date' => $today],
            [
                'total_revenue_in_cents' => 0,
                'order_count' => 0,
                'line_item_count' => 0,
            ]
        );

        DailyRevenueStat::where('date', $today)->increment('total_revenue_in_cents', $lineTotal);
        DailyRevenueStat::where('date', $today)->increment('line_item_count', $event->quantity);
    }

    public function onOrderCancelled(OrderCancelled $event, string $aggregateUuid): void
    {
        $today = now()->toDateString();

        $order = Order::where('uuid', $aggregateUuid)->first();

        if ($order) {
            DailyRevenueStat::where('date', $today)->decrement(
                'total_revenue_in_cents',
                $order->total_in_cents
            );
            DailyRevenueStat::where('date', $today)->decrement('order_count');
        }
    }
}
