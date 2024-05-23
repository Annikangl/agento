<?php

namespace App\Exceptions\Api\System;

use App\Exceptions\Api\ApiException;
use Illuminate\Http\Response;

class ModelNotFoundException extends ApiException
{
    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
