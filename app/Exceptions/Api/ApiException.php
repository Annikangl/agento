<?php

namespace App\Exceptions\Api;

use App\Contracts\HasStatusCode;
use Exception;
use Illuminate\Http\JsonResponse;

abstract class ApiException extends Exception implements HasStatusCode
{
    public function render($request): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $this->getMessage(),
            'type' => static::class,
        ])
            ->setStatusCode($this->getStatusCode());
    }

    public function getStatusCode(): int
    {
        if ($this->getCode() != 0 && $this->getCode()) {
            return  $this->getCode();
        }

        return 500;
    }
}
