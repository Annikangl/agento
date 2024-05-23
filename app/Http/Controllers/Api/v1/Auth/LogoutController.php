<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Exceptions\Api\Auth\LogoutException;
use App\Http\Controllers\Controller;
use App\UseCases\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class LogoutController extends Controller
{
    /**
     * Logout auth user
     * @param AuthService $authService
     * @return JsonResponse
     * @throws LogoutException
     */
    public function __invoke(AuthService $authService): JsonResponse
    {
        $authService->logout(auth('sanctum')->user());

        return response()->json(['status' => true, 'message' => 'Logout successful'])
            ->setStatusCode(Response::HTTP_OK);
    }
}
