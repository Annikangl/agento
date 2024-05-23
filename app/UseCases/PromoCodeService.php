<?php

namespace App\UseCases;

use App\Exceptions\Api\Promocode\PromocodeException;
use App\Models\User\Promocode;
use App\Models\User\User;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use MoonShine\Models\MoonshineUser;

class PromoCodeService
{
    /**
     * Generate unique promocode
     * @return string
     */
    public function generate(): string
    {
        return Promocode::generateUniquePromoCode();
    }

    /**
     * Create promocode by user
     * @param User $user
     * @return void
     * @throws PromocodeException
     */
    public function create(User $user): void
    {
        try {
            $promocode = $this->generate();

            $user->promocode()->create([
                'code' => $promocode,
                'discount' => Promocode::BASE_DISCOUNT_PERCENT,
            ]);
        } catch (\Throwable $throwable) {
            throw new PromocodeException($throwable->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Find and check promocode.
     * Promocode must be existed, not own, not expired and available
     * @param string $code
     * @param int $currentUserId
     * @return Promocode
     * @throws PromocodeException
     */
    public function find(string $code, int $currentUserId): Promocode
    {
        $promoCode = Promocode::query()->where('code', $code)->first();

        if (!$promoCode) {
            throw new PromocodeException(__(
                'messages.promocodes.not_found'),
                Response::HTTP_NOT_FOUND
            );
        }

        if ($promoCode->user_id === $currentUserId) {
            throw new PromocodeException(
                __('messages.promocodes.cannot_activate_own_promocode'),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if ($promoCode->expired_at && Carbon::parse($promoCode->expired_at)->lessThan(Carbon::now())) {
            throw new PromocodeException(
                __('messages.promocodes.expired'),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if ($promoCode->usage_limit !== null && $promoCode->used_count >= $promoCode->usage_limit) {
            throw new PromocodeException(
                __('messages.promocodes.anvialable'),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return $promoCode;
    }

    /**
     * Activate promocode and associate to sepervisor
     * @param Promocode $promocode
     * @throws PromocodeException
     */
    public function activate(Promocode $promocode): void
    {
        try {
            $promocode->used_count += 1;
            $promocode->updated_at = now();
            $promocode->save();

        } catch (\Throwable $exception) {
            throw new PromocodeException($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
