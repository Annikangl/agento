<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\DTOs\User\UserCreaeDto;
use App\Exceptions\Api\Auth\RegisterException;
use App\Exceptions\Api\Promocode\PromocodeException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\User\UserResource;
use App\UseCases\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RegisterController extends Controller
{
    /**
     * Register a new user and generate referal promocode and create empty balance
     * @param RegisterRequest $request
     * @param AuthService $authService
     * @return JsonResponse
     * @throws PromocodeException
     * @throws \Throwable
     * @throws RegisterException
     */
    public function __invoke(RegisterRequest $request, AuthService $authService,): JsonResponse
    {
        $dto = UserCreaeDto::fromArray($request->validated());
        $user = $authService->register($dto);

        return response()->json([
            'status' => true,
            'token' => $user->createToken($user->device_name)->plainTextToken,
            'user' => new UserResource($user),
        ])->setStatusCode(Response::HTTP_CREATED);
    }
}
