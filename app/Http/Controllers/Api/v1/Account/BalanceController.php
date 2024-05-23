<?php

namespace App\Http\Controllers\Api\v1\Account;

use App\Exceptions\Api\User\CreateWithdrawalException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\CreateWithdrawalRequest;
use App\Http\Resources\User\Account\UserBalanceResource;
use App\Http\Resources\User\Account\WithdrawalResource;
use App\UseCases\Account\BalanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class BalanceController extends Controller
{
    public function __construct(private readonly BalanceService $balanceService)
    {
    }

    /**
     * Get user balance
     * @return JsonResponse
     */
    public function getBalance()
    {
        $user = auth()->user();

        return response()->json([
            'status' => true,
            'balance' => new UserBalanceResource($user->balance)
        ])
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @param CreateWithdrawalRequest $request
     * @return JsonResponse
     * @throws CreateWithdrawalException
     */
    public function createWithdrawal(CreateWithdrawalRequest $request)
    {
        $withdrawal = $this->balanceService->createWithdrawal(
            auth()->user(),
            $request->validated('amount')
        );

        return \response()->json(['status' => true, 'withdrawal_request' => new WithdrawalResource($withdrawal)])
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
