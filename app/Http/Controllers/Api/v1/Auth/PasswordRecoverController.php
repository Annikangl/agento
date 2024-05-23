<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Exceptions\Api\Auth\PasswordException;
use App\Exceptions\Api\System\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordRecoverRequest;
use App\UseCases\AuthService;
use Illuminate\Http\Response;

class PasswordRecoverController extends Controller
{
    /**
     * @throws ModelNotFoundException|PasswordException
     */
    public function __invoke(PasswordRecoverRequest $request, AuthService $authService)
    {
        $authService->recoverPassword(
            $request->validated('email'),
            $request->validated('password')
        );

        return response()->json(['status' => true, 'message' => 'Password changed'])
            ->setStatusCode(Response::HTTP_OK);
    }
}
