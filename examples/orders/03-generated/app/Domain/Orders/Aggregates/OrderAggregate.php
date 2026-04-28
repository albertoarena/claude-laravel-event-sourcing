<?php

namespace App\Domain\Orders\Aggregates;

use App\Domain\Orders\Events\LineItemAdded;
use App\Domain\Orders\Events\OrderCancelled;
use App\Domain\Orders\Events\OrderPlaced;
use App\Domain\Orders\Events\OrderShipped;
use App\Domain\Orders\Exceptions\CannotCancelShippedOrder;
use App\Domain\Orders\Exceptions\OrderAlreadyCancelled;
use App\Domain\Orders\Exceptions\OrderAlreadyShipped;
use App\Domain\Orders\Exceptions\OrderNotPlaced;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class OrderAggregate extends AggregateRoot
{
    protected bool $isPlaced = false;
    protected bool $isShipped = false;
    protected bool $isCancelled = false;

    public function place(string $customerId, string $customerEmail): self
    {
        $this->recordThat(new OrderPlaced(
            customerId: $customerId,
            customerEmail: $customerEmail,
        ));

        return $this;
    }

    public function addLineItem(string $sku, int $quantity, int $priceInCents, string $description): self
    {
        $this->guardAgainstUnplacedOrder();
        $this->guardAgainstShippedOrder();
        $this->guardAgainstCancelledOrder();

        $this->recordThat(new LineItemAdded(
            sku: $sku,
            quantity: $quantity,
            priceInCents: $priceInCents,
            description: $description,
        ));

        return $this;
    }

    public function ship(string $shippedAt): self
    {
        $this->guardAgainstUnplacedOrder();
        $this->guardAgainstShippedOrder();
        $this->guardAgainstCancelledOrder();

        $this->recordThat(new OrderShipped(
            shippedAt: $shippedAt,
        ));

        return $this;
    }

    public function cancel(string $cancelledAt, string $reason): self
    {
        $this->guardAgainstUnplacedOrder();

        if ($this->isShipped) {
            throw new CannotCancelShippedOrder();
        }

        $this->guardAgainstCancelledOrder();

        $this->recordThat(new OrderCancelled(
            cancelledAt: $cancelledAt,
            reason: $reason,
        ));

        return $this;
    }

    protected function applyOrderPlaced(OrderPlaced $event): void
    {
        $this->isPlaced = true;
    }

    protected function applyOrderShipped(OrderShipped $event): void
    {
        $this->isShipped = true;
    }

    protected function applyOrderCancelled(OrderCancelled $event): void
    {
        $this->isCancelled = true;
    }

    private function guardAgainstUnplacedOrder(): void
    {
        if (! $this->isPlaced) {
            throw new OrderNotPlaced();
        }
    }

    private function guardAgainstShippedOrder(): void
    {
        if ($this->isShipped) {
            throw new OrderAlreadyShipped();
        }
    }

    private function guardAgainstCancelledOrder(): void
    {
        if ($this->isCancelled) {
            throw new OrderAlreadyCancelled();
        }
    }
}
