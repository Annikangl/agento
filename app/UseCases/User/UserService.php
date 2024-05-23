<?php

namespace App\UseCases\User;

use App\DTOs\User\UserCreaeDto;
use App\Exceptions\Api\Cabinet\CabinetException;
use App\Models\User\User;
use App\UseCases\CommercialOfferService;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function create(UserCreaeDto $dto): User
    {
        $user = new User();
        $user->name = $dto->name;
        $user->country = $dto->country;
        $user->email = $dto->email;
        $user->phone = $dto->phone;
        $user->password = Hash::make($dto->password);
        $user->fcm_token = $dto->fcm_token;
        $user->device_name = $dto->device_name;

        $user->save();

        return $user;
    }

    /**
     * Update user personal information for current auth user
     * @param User $user
     * @param array $requestData
     * @return User
     * @throws CabinetException
     */
    public function update(User $user, array $requestData): User
    {
        if (Carbon::now()->diffInDays($user->updated_at) < User::ALLOWED_UPDATE_DAYS) {
            $formattedDate = $user->updated_at->addDays(User::ALLOWED_UPDATE_DAYS)->format('d.m.Y');
            throw new CabinetException($formattedDate, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $userData = collect($requestData)
            ->filter()
            ->toArray();

        $user->update($userData);

        return $user;
    }

    /**
     * Set new password for current user
     * @param User $user
     * @param string $newPassword
     * @return void
     * @throws CabinetException
     */
    public function updatePassword(User $user, string $newPassword): void
    {
        if (password_verify($newPassword, $user->password)) {
            throw new CabinetException(
                'The password must not be equal to the old password',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            $user->password = bcrypt($newPassword);
            $user->saveOrFail();
        } catch (\Throwable $e) {
            throw new CabinetException(
                'The password could not be changed. Try again later',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Delete user account with all relations
     * @param User $user
     * @return void
     */
    public function delete(User $user): void
    {
        $offerService = app(CommercialOfferService::class);

        $user->commercialOffers->each(function ($item) use ($offerService) {
            $offerService->delete($item);
        });

        $user->delete();
    }

    public function getById(int $id): User
    {
        $user = User::findOrFail($id);

        return $user->load(['promocode', 'balance','commercialOffers']);
    }
}
