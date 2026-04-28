<?php

use App\Domain\Orders\Events\OrderShipped;
use App\Domain\Orders\Reactors\SendShipConfirmationReactor;
use App\Domain\Orders\ReadModels\Order;
use App\Mail\OrderShippedMail;
use Illuminate\Support\Facades\Mail;

it('sends ship confirmation email on OrderShipped', function () {
    Mail::fake();

    // Set up the order read model (the sync OrderProjector would have done this)
    Order::create([
        'uuid' => 'order-1',
        'customer_id' => 'cust-1',
        'customer_email' => 'customer@example.com',
        'status' => 'placed',
        'line_items' => [],
        'total_in_cents' => 0,
    ]);

    $event = new OrderShipped(shippedAt: '2026-04-20T10:00:00Z');

    (new SendShipConfirmationReactor())->onOrderShipped($event, aggregateUuid: 'order-1');

    Mail::assertSent(OrderShippedMail::class, function (OrderShippedMail $mail) {
        return $mail->orderUuid === 'order-1'
            && $mail->shippedAt === '2026-04-20T10:00:00Z';
    });
});

it('does not send email if order not found', function () {
    Mail::fake();

    $event = new OrderShipped(shippedAt: '2026-04-20T10:00:00Z');

    (new SendShipConfirmationReactor())->onOrderShipped($event, aggregateUuid: 'nonexistent-order');

    Mail::assertNothingSent();
});
