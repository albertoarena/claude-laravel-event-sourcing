<?php

use App\Domain\Orders\Events\LineItemAdded;
use App\Domain\Orders\Events\OrderCancelled;
use App\Domain\Orders\Events\OrderPlaced;
use App\Domain\Orders\Events\OrderShipped;
use App\Domain\Orders\Projectors\OrderProjector;
use App\Domain\Orders\ReadModels\Order;

it('creates an order row on OrderPlaced', function () {
    $event = new OrderPlaced(
        customerId: 'cust-1',
        customerEmail: 'customer@example.com',
    );

    (new OrderProjector())->onOrderPlaced($event, aggregateUuid: 'order-1');

    $order = Order::find('order-1');
    expect($order)->not->toBeNull()
        ->and($order->customer_id)->toBe('cust-1')
        ->and($order->customer_email)->toBe('customer@example.com')
        ->and($order->status)->toBe('placed')
        ->and($order->total_in_cents)->toBe(0)
        ->and($order->line_items)->toBe([]);
});

it('adds a line item and updates total on LineItemAdded', function () {
    $projector = new OrderProjector();

    $projector->onOrderPlaced(
        new OrderPlaced(customerId: 'cust-1', customerEmail: 'customer@example.com'),
        aggregateUuid: 'order-1'
    );

    $projector->onLineItemAdded(
        new LineItemAdded(sku: 'WIDGET-1', quantity: 2, priceInCents: 1999, description: 'Blue Widget'),
        aggregateUuid: 'order-1'
    );

    $order = Order::find('order-1');
    expect($order->line_items)->toHaveCount(1)
        ->and($order->line_items[0]['sku'])->toBe('WIDGET-1')
        ->and($order->total_in_cents)->toBe(3998);
});

it('updates status to shipped on OrderShipped', function () {
    $projector = new OrderProjector();

    $projector->onOrderPlaced(
        new OrderPlaced(customerId: 'cust-1', customerEmail: 'customer@example.com'),
        aggregateUuid: 'order-1'
    );

    $projector->onOrderShipped(
        new OrderShipped(shippedAt: '2026-04-20T10:00:00Z'),
        aggregateUuid: 'order-1'
    );

    $order = Order::find('order-1');
    expect($order->status)->toBe('shipped')
        ->and($order->shipped_at)->not->toBeNull();
});

it('updates status to cancelled on OrderCancelled', function () {
    $projector = new OrderProjector();

    $projector->onOrderPlaced(
        new OrderPlaced(customerId: 'cust-1', customerEmail: 'customer@example.com'),
        aggregateUuid: 'order-1'
    );

    $projector->onOrderCancelled(
        new OrderCancelled(cancelledAt: '2026-04-20T11:00:00Z', reason: 'Changed my mind'),
        aggregateUuid: 'order-1'
    );

    $order = Order::find('order-1');
    expect($order->status)->toBe('cancelled')
        ->and($order->cancellation_reason)->toBe('Changed my mind');
});

it('is idempotent -- handles the same OrderPlaced event twice', function () {
    $event = new OrderPlaced(
        customerId: 'cust-1',
        customerEmail: 'customer@example.com',
    );

    $projector = new OrderProjector();
    $projector->onOrderPlaced($event, aggregateUuid: 'order-1');
    $projector->onOrderPlaced($event, aggregateUuid: 'order-1');

    expect(Order::where('uuid', 'order-1')->count())->toBe(1);
});
