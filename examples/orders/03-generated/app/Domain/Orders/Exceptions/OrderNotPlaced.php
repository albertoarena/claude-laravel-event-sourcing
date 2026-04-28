<?php

namespace App\Domain\Orders\Exceptions;

use DomainException;

final class OrderNotPlaced extends DomainException
{
    public function __construct()
    {
        parent::__construct('Cannot perform this action on an order that has not been placed.');
    }
}
