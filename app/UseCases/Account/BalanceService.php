<?php

namespace App\UseCases\Account;

use App\Enums\User\WithdrawalStatus;
use App\Exceptions\Api\User\CreateBalanceException;
use App\Exceptions\Api\User\CreateWithdrawalException;
use App\Models\User\User;
use App\Models\User\Withdrawal;
use Illuminate\Http\Response;

class BalanceService
{
    /**
     * Create empty balance for user
     * @param User $user
     * @return void
     * @throws CreateBalanceException
     */
    public function createEmptyBalance(User $user): void
    {
        try {
            $user->balance()->create([
                'amount' => 0,
                'can_withdrawal' => true,
            ]);
        } catch (\Throwable $exception) {
            throw new CreateBalanceException($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Create withdrawal request
     * @param User $user
     * @param int $withdrawalAmount
     * @return Withdrawal
     * @throws CreateWithdrawalException
     */
    public function createWithdrawal(User $user, int $withdrawalAmount): Withdrawal
    {
        if (!$user->canWithdrawal($withdrawalAmount)) {
            throw new CreateWithdrawalException(
                __('messages.withdrawals.Insufficient balance'),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        try {
            $withdrawal = $user->withdrawals()->create([
                'amount' => $withdrawalAmount,
                'status' => WithdrawalStatus::PENDING
            ]);
        } catch (\Throwable $exception) {
            throw new CreateWithdrawalException($exception->getMessage());
        }

        return $withdrawal;
    }
}
