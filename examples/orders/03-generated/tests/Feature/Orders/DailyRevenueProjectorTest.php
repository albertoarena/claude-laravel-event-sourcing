<?php

use App\Domain\Orders\Events\LineItemAdded;
use App\Domain\Orders\Events\OrderPlaced;
use App\Domain\Orders\Projectors\DailyRevenueProjector;
use App\Domain\Orders\ReadModels\DailyRevenueStat;

it('increments order count on OrderPlaced', function () {
    $projector = new DailyRevenueProjector();

    $projector->onOrderPlaced(
        new OrderPlaced(customerId: 'cust-1', customerEmail: 'customer@example.com'),
        aggregateUuid: 'order-1'
    );

    $stat = DailyRevenueStat::where('date', now()->toDateString())->first();
    expect($stat)->not->toBeNull()
        ->and($stat->order_count)->toBe(1);
});

it('increments revenue and line item count on LineItemAdded', function () {
    $projector = new DailyRevenueProjector();

    $projector->onOrderPlaced(
        new OrderPlaced(customerId: 'cust-1', customerEmail: 'customer@example.com'),
        aggregateUuid: 'order-1'
    );

    $projector->onLineItemAdded(
        new LineItemAdded(sku: 'WIDGET-1', quantity: 3, priceInCents: 1000, description: 'Widget'),
        aggregateUuid: 'order-1'
    );

    $stat = DailyRevenueStat::where('date', now()->toDateString())->first();
    expect($stat->total_revenue_in_cents)->toBe(3000)
        ->and($stat->line_item_count)->toBe(3);
});

it('accumulates revenue across multiple line items', function () {
    $projector = new DailyRevenueProjector();

    $projector->onOrderPlaced(
        new OrderPlaced(customerId: 'cust-1', customerEmail: 'customer@example.com'),
        aggregateUuid: 'order-1'
    );

    $projector->onLineItemAdded(
        new LineItemAdded(sku: 'WIDGET-1', quantity: 1, priceInCents: 1000, description: 'Widget A'),
        aggregateUuid: 'order-1'
    );

    $projector->onLineItemAdded(
        new LineItemAdded(sku: 'WIDGET-2', quantity: 2, priceInCents: 500, description: 'Widget B'),
        aggregateUuid: 'order-1'
    );

    $stat = DailyRevenueStat::where('date', now()->toDateString())->first();
    expect($stat->total_revenue_in_cents)->toBe(2000)
        ->and($stat->line_item_count)->toBe(3);
});
