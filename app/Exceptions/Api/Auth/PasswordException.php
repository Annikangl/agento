<?php

namespace App\Exceptions\Api\Auth;

use App\Traits\ApiException;

class PasswordException extends \Exception
{
    use ApiException;
}
