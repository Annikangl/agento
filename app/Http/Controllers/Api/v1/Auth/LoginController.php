<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Exceptions\Api\Auth\LoginException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\User\UserResource;
use App\UseCases\AuthService;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    /**
     * Login user by credentials
     * @param LoginRequest $request
     * @param AuthService $authService
     * @return JsonResponse
     * @throws LoginException
     */
    public function __invoke(LoginRequest $request, AuthService $authService)
    {
        $user = $authService->login(
            $request->validated('email'),
            $request->validated('password'),
            $request->validated('fcm_token')
        );

        return response()->json([
            'status' => true,
            'token' => $user->createToken($request->validated('device_name'))->plainTextToken,
            'user' => new UserResource($user)
        ])->setStatusCode(200);
    }
}
