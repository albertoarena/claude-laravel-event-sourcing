<?php

use App\Domain\Orders\Aggregates\OrderAggregate;
use App\Domain\Orders\Events\LineItemAdded;
use App\Domain\Orders\Events\OrderCancelled;
use App\Domain\Orders\Events\OrderPlaced;
use App\Domain\Orders\Events\OrderShipped;
use App\Domain\Orders\Exceptions\CannotCancelShippedOrder;
use App\Domain\Orders\Exceptions\OrderAlreadyCancelled;
use App\Domain\Orders\Exceptions\OrderAlreadyShipped;
use App\Domain\Orders\Exceptions\OrderNotPlaced;

it('records OrderPlaced when placing an order', function () {
    $uuid = fake()->uuid();

    OrderAggregate::fake($uuid)
        ->when(fn (OrderAggregate $a) => $a->place(
            customerId: 'cust-1',
            customerEmail: 'customer@example.com',
        ))
        ->assertRecorded([
            new OrderPlaced(
                customerId: 'cust-1',
                customerEmail: 'customer@example.com',
            ),
        ]);
});

it('records LineItemAdded when adding a line item to a placed order', function () {
    $uuid = fake()->uuid();

    OrderAggregate::fake($uuid)
        ->given([
            new OrderPlaced(customerId: 'cust-1', customerEmail: 'customer@example.com'),
        ])
        ->when(fn (OrderAggregate $a) => $a->addLineItem(
            sku: 'WIDGET-1',
            quantity: 2,
            priceInCents: 1999,
            description: 'Blue Widget',
        ))
        ->assertRecorded([
            new LineItemAdded(
                sku: 'WIDGET-1',
                quantity: 2,
                priceInCents: 1999,
                description: 'Blue Widget',
            ),
        ]);
});

it('records OrderShipped when shipping a placed order', function () {
    $uuid = fake()->uuid();

    OrderAggregate::fake($uuid)
        ->given([
            new OrderPlaced(customerId: 'cust-1', customerEmail: 'customer@example.com'),
        ])
        ->when(fn (OrderAggregate $a) => $a->ship(
            shippedAt: '2026-04-20T10:00:00Z',
        ))
        ->assertRecorded([
            new OrderShipped(shippedAt: '2026-04-20T10:00:00Z'),
        ]);
});

it('records OrderCancelled when cancelling a placed order', function () {
    $uuid = fake()->uuid();

    OrderAggregate::fake($uuid)
        ->given([
            new OrderPlaced(customerId: 'cust-1', customerEmail: 'customer@example.com'),
        ])
        ->when(fn (OrderAggregate $a) => $a->cancel(
            cancelledAt: '2026-04-20T11:00:00Z',
            reason: 'Changed my mind',
        ))
        ->assertRecorded([
            new OrderCancelled(
                cancelledAt: '2026-04-20T11:00:00Z',
                reason: 'Changed my mind',
            ),
        ]);
});

it('refuses to cancel a shipped order', function () {
    $uuid = fake()->uuid();

    OrderAggregate::fake($uuid)
        ->given([
            new OrderPlaced(customerId: 'cust-1', customerEmail: 'customer@example.com'),
            new OrderShipped(shippedAt: '2026-04-20T10:00:00Z'),
        ])
        ->when(fn (OrderAggregate $a) => $a->cancel(
            cancelledAt: '2026-04-20T11:00:00Z',
            reason: 'Too late',
        ))
        ->assertNothingRecorded()
        ->assertExceptionThrown(CannotCancelShippedOrder::class);
});

it('refuses to ship an already shipped order', function () {
    $uuid = fake()->uuid();

    OrderAggregate::fake($uuid)
        ->given([
            new OrderPlaced(customerId: 'cust-1', customerEmail: 'customer@example.com'),
            new OrderShipped(shippedAt: '2026-04-20T10:00:00Z'),
        ])
        ->when(fn (OrderAggregate $a) => $a->ship(
            shippedAt: '2026-04-20T12:00:00Z',
        ))
        ->assertNothingRecorded()
        ->assertExceptionThrown(OrderAlreadyShipped::class);
});

it('refuses to add line items to a shipped order', function () {
    $uuid = fake()->uuid();

    OrderAggregate::fake($uuid)
        ->given([
            new OrderPlaced(customerId: 'cust-1', customerEmail: 'customer@example.com'),
            new OrderShipped(shippedAt: '2026-04-20T10:00:00Z'),
        ])
        ->when(fn (OrderAggregate $a) => $a->addLineItem(
            sku: 'WIDGET-2',
            quantity: 1,
            priceInCents: 500,
            description: 'Red Widget',
        ))
        ->assertNothingRecorded()
        ->assertExceptionThrown(OrderAlreadyShipped::class);
});

it('refuses to add line items to a cancelled order', function () {
    $uuid = fake()->uuid();

    OrderAggregate::fake($uuid)
        ->given([
            new OrderPlaced(customerId: 'cust-1', customerEmail: 'customer@example.com'),
            new OrderCancelled(cancelledAt: '2026-04-20T11:00:00Z', reason: 'No longer needed'),
        ])
        ->when(fn (OrderAggregate $a) => $a->addLineItem(
            sku: 'WIDGET-2',
            quantity: 1,
            priceInCents: 500,
            description: 'Red Widget',
        ))
        ->assertNothingRecorded()
        ->assertExceptionThrown(OrderAlreadyCancelled::class);
});

it('refuses to ship a cancelled order', function () {
    $uuid = fake()->uuid();

    OrderAggregate::fake($uuid)
        ->given([
            new OrderPlaced(customerId: 'cust-1', customerEmail: 'customer@example.com'),
            new OrderCancelled(cancelledAt: '2026-04-20T11:00:00Z', reason: 'No longer needed'),
        ])
        ->when(fn (OrderAggregate $a) => $a->ship(
            shippedAt: '2026-04-20T12:00:00Z',
        ))
        ->assertNothingRecorded()
        ->assertExceptionThrown(OrderAlreadyCancelled::class);
});

it('refuses to add a line item before order is placed', function () {
    $uuid = fake()->uuid();

    OrderAggregate::fake($uuid)
        ->when(fn (OrderAggregate $a) => $a->addLineItem(
            sku: 'WIDGET-1',
            quantity: 1,
            priceInCents: 999,
            description: 'Widget',
        ))
        ->assertNothingRecorded()
        ->assertExceptionThrown(OrderNotPlaced::class);
});

it('refuses to ship before order is placed', function () {
    $uuid = fake()->uuid();

    OrderAggregate::fake($uuid)
        ->when(fn (OrderAggregate $a) => $a->ship(
            shippedAt: '2026-04-20T10:00:00Z',
        ))
        ->assertNothingRecorded()
        ->assertExceptionThrown(OrderNotPlaced::class);
});

it('refuses to cancel before order is placed', function () {
    $uuid = fake()->uuid();

    OrderAggregate::fake($uuid)
        ->when(fn (OrderAggregate $a) => $a->cancel(
            cancelledAt: '2026-04-20T11:00:00Z',
            reason: 'No order yet',
        ))
        ->assertNothingRecorded()
        ->assertExceptionThrown(OrderNotPlaced::class);
});
