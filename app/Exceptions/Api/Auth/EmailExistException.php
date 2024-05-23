<?php

namespace App\Exceptions\Api\Auth;

use App\Traits\ApiException;
use Exception;

class EmailExistException extends Exception
{
    use ApiException;
}
