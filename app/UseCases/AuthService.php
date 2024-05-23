<?php

namespace App\UseCases;

use App\DTOs\User\UserCreaeDto;
use App\Exceptions\Api\Auth\LoginException;
use App\Exceptions\Api\Auth\LogoutException;
use App\Exceptions\Api\Auth\PasswordException;
use App\Exceptions\Api\Auth\RegisterException;
use App\Exceptions\Api\Promocode\PromocodeException;
use App\Exceptions\Api\System\ModelNotFoundException;
use App\Models\User\User;
use App\Models\User\VerificationCode;
use App\Pipes\User\ClearVerificationCode;
use App\Pipes\User\CreateBalance;
use App\Pipes\User\GenerateAndCreatePromocode;
use App\Pipes\User\GiveSubscription;
use App\UseCases\User\UserService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Pipeline;

class AuthService
{
    public function __construct(
        private readonly UserService      $userService,
        private readonly PromoCodeService $promoCodeService,
        private readonly ReferralService  $referralService
    )
    {
    }

    /**
     * Create a new user and clear verification code
     * @param UserCreaeDto $dto
     * @return User
     * @throws RegisterException
     * @throws PromocodeException
     */
    public function register(UserCreaeDto $dto): User
    {
        try {
            $user = DB::transaction(function () use ($dto) {
                $user = $this->userService->create($dto);

                return Pipeline::send($user)
                    ->through([
                        CreateBalance::class,
                        GenerateAndCreatePromocode::class,
                        GiveSubscription::class,
                        ClearVerificationCode::class
                    ])
                    ->thenReturn();
            });

        } catch (\Throwable $throwable) {
            throw new RegisterException(
                $throwable->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        if ($dto->promocode) {
            $promocode = $this->promoCodeService->find($dto->promocode, $user->id);
            $this->promoCodeService->activate($promocode);

            $this->referralService->addReferral(
                $promocode->user,
                $user
            );
        }

        return $user;
    }

    /**
     * Login user by credentials
     * @param string $email
     * @param string $password
     * @param string|null $fcmToken
     * @return User
     * @throws LoginException
     */
    public function login(string $email, string $password, ?string $fcmToken): User
    {
        $user = $this->getUserByEmail($email);

        if (! $user || !Hash::check($password, $user->password)) {
            throw new LoginException('Invalid email or password', Response::HTTP_UNAUTHORIZED);
        }

        $user->updateFcmToken($fcmToken);

        return $user;
    }

    /**
     * Logout user and set null fcm token
     * @param User|Authenticatable|null $user
     * @return void
     * @throws LogoutException
     */
    public function logout(User|Authenticatable|null $user): void
    {
        try {
            $this->checkUserExists($user);
            $user->updateFcmToken(null);
            $user->tokens()->delete();
        } catch (\Throwable $throwable) {
            throw new LogoutException($throwable->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Recover user password
     * @param string $email
     * @param string $newPassword
     * @throws ModelNotFoundException
     * @throws PasswordException
     */
    public function recoverPassword(string $email, string $newPassword): void
    {
        $user = $this->getUserByEmail($email);

        $this->checkUserExists($user);

        if (Hash::check($newPassword, $user->password)) {
            throw new PasswordException(
                'The new password must not be equal to the old password',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        VerificationCode::clearVerificationCode($user->email);
    }

    /**
     * Get user by email
     * @param string $email
     * @return User|null
     */
    private function getUserByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }

    /**
     * Check user
     * @throws ModelNotFoundException
     */
    private function checkUserExists(?User $user): void
    {
        if (!$user) {
            throw new ModelNotFoundException('User not found', Response::HTTP_NOT_FOUND);
        }
    }
}
