<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

trait JsonFailedValidation
{
    protected function failedValidation(Validator $validator): void
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(
                response()->json(["status" => false, "message" => $this->validator->errors()->first()],
                    Response::HTTP_UNPROCESSABLE_ENTITY));
        }
    }
}
