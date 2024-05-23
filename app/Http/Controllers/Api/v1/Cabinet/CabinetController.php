<?php

namespace App\Http\Controllers\Api\v1\Cabinet;

use App\Exceptions\Api\Cabinet\CabinetException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cabinet\ChangeUserPasswordRequest;
use App\Http\Requests\Cabinet\UpdateUserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User\User;
use App\Models\User\VerificationCode;
use App\UseCases\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CabinetController extends Controller
{
    public function __construct(private readonly UserService $userService)
    {
    }

    /**\
     * Returns auth user info
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $user = $this->userService->getById(Auth::id());

        return response()->json(['status' => true, 'user' => new UserResource($user)])
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @param UpdateUserRequest $request
     * @return JsonResponse
     * @throws CabinetException
     */
    public function update(UpdateUserRequest $request): JsonResponse
    {
        $user = $this->userService->update(
            user: Auth::user(),
            requestData: $request->validated()
        );

        return response()->json(['status' => true, 'user' => new UserResource($user)])
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @param ChangeUserPasswordRequest $request
     * @return JsonResponse
     * @throws CabinetException
     */
    public function chanePassword(ChangeUserPasswordRequest $request): JsonResponse
    {
        $this->userService->updatePassword(
            user: Auth::user(),
            newPassword: $request->validated('new_password')
        );

        VerificationCode::clearVerificationCode(Auth::user()->email);

        return response()->json(['status' => true, 'message' => 'Password changed succesful'])
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Delete user account
     * @param User|null $user
     * @return JsonResponse
     */
    public function delete(?User $user = null): JsonResponse
    {
        $user = $user ?? Auth::guard('sanctum')->user();

        $this->userService->delete($user);

        return response()->json(['status' => true, 'message' => "Account $user->id deleted"])
            ->setStatusCode(Response::HTTP_OK);
    }
}
