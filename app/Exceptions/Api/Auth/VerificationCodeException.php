<?php

namespace App\Exceptions\Api\Auth;

use App\Traits\ApiException;
use Exception;

class VerificationCodeException extends Exception
{
    use ApiException;
}
