<?php

namespace App\Exceptions\Api\Auth;

use App\Traits\ApiException;

class LogoutException extends \Exception
{
    use ApiException;
}
