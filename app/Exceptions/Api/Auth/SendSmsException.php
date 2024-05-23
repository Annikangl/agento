<?php

namespace App\Exceptions\Api\Auth;

use App\Traits\ApiException;
use Exception;

class SendSmsException extends Exception
{
    use ApiException;
}
