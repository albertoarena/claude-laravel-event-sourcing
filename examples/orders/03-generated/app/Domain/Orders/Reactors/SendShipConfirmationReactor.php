<?php

namespace App\Domain\Orders\Reactors;

use App\Domain\Orders\Events\OrderShipped;
use App\Domain\Orders\ReadModels\Order;
use App\Mail\OrderShippedMail;
use Illuminate\Support\Facades\Mail;
use Spatie\EventSourcing\EventHandlers\EventHandler;

class SendShipConfirmationReactor extends EventHandler
{
    public function onOrderShipped(OrderShipped $event, string $aggregateUuid): void
    {
        $order = Order::where('uuid', $aggregateUuid)->first();

        if ($order) {
            Mail::to($order->customer_email)
                ->send(new OrderShippedMail(
                    orderUuid: $aggregateUuid,
                    shippedAt: $event->shippedAt,
                ));
        }
    }
}
