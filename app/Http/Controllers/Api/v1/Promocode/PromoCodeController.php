<?php

namespace App\Http\Controllers\Api\v1\Promocode;

use App\Exceptions\Api\Promocode\PromocodeException;
use App\Http\Controllers\Controller;
use App\Http\Requests\PromoCodeRequest;
use App\Http\Resources\User\PromoCodeResource;
use App\Models\User\Promocode;
use App\Models\User\User;
use App\UseCases\PromoCodeService;
use App\UseCases\ReferralService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use MoonShine\Models\MoonshineUser;

class PromoCodeController extends Controller
{
    /**
     * @param PromoCodeRequest $request
     * @param PromoCodeService $service
     * @param ReferralService $referralService
     * @return JsonResponse
     * @throws PromocodeException
     */
    public function activate(PromoCodeRequest $request, PromoCodeService $service, ReferralService $referralService): JsonResponse
    {
        $promoCode = $service->find(
            $request->validated('code'),
            $request->user()->id
        );

        try {
            $service->activate($promoCode);
            $referralService->addReferral($promoCode->user, $request->user());
        } catch (\Throwable $exception) {
            throw new PromocodeException($exception->getMessage(), 500);
        }

        return response()->json([
            'status' => true,
            'promocode' => new PromoCodeResource($promoCode),
            'message' => __('messages.promocodes.activated')
        ])
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
