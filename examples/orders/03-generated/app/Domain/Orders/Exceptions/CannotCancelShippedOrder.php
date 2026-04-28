<?php

namespace App\Domain\Orders\Exceptions;

use DomainException;

final class CannotCancelShippedOrder extends DomainException
{
    public function __construct()
    {
        parent::__construct('Cannot cancel an order that has already been shipped.');
    }
}
