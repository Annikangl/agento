<?php

namespace App\Exceptions\Api\Auth\Ticket;

use App\Traits\ApiException;

class CreateTicketException extends \Exception
{
    use ApiException;
}
