<?php

namespace App\Domain\Orders\Exceptions;

use DomainException;

final class OrderAlreadyShipped extends DomainException
{
    public function __construct()
    {
        parent::__construct('This order has already been shipped.');
    }
}
