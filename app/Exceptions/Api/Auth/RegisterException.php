<?php

namespace App\Exceptions\Api\Auth;

use App\Traits\ApiException;

class RegisterException extends \Exception
{
    use ApiException;
}
