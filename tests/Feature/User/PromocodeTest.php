<?php

namespace Tests\Feature\User;

use App\Exceptions\Api\Promocode\PromocodeException;
use App\Models\User\Plan;
use App\Models\User\Promocode;
use App\Models\User\User;
use App\Pipes\User\CreateBalance;
use App\Pipes\User\GenerateAndCreatePromocode;
use App\Pipes\User\GiveSubscription;
use App\UseCases\PromoCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Pipeline;
use Tests\TestCase;

class PromocodeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Plan::factory()->create([
            'price' => 0
        ]);
    }

    /**
     * @throws PromocodeException
     */
    public function test_create_and_generate_promocode()
    {
        $user = User::factory()->create();
        $this->createUserSubscription($user);

        $promocodeService = app(PromoCodeService::class);

        $promocodeService->create($user);

        $this->assertDatabaseHas('promocodes', [
            'user_id' => $user->id,
            'discount' => Promocode::BASE_DISCOUNT_PERCENT,
            'code' => $user->promocode->code,
        ]);
    }

    /**
     * Test if user can activate exist promocode
     * @throws PromocodeException
     */
    public function test_user_can_activate_promocode(): void
    {
        $user = User::factory()->create();
        $referal = User::factory()->create();


        $this->createUserSubscription($user);
        $this->createUserSubscription($referal);

        $promocodeService = app(PromoCodeService::class);

        $promocodeService->create($user);

        $response = $this->postJson(route('promocode.activate', [
            'code' => $user->promocode->code
        ]));

        $response->assertStatus(201);

        $response->assertJson([
            'status' => true,
        ]);

        $this->assertDatabaseHas('user_referrals', [
            'referrer_id' => $user->id,
            'referral_id' => $referal->id,
            'level' => 1,
        ]);
    }

    /**
     * Test if user cant activate self promocode
     * @throws PromocodeException
     */
    public function test_user_cant_self_activate_promocode(): void
    {
        $user = User::factory()->create();
        $this->createUserSubscription($user);

        $promocodeService = app(PromoCodeService::class);

        $promocodeService->create($user);

        $response = $this->postJson(route('promocode.activate', [
            'code' => $user->promocode->code
        ]));

        $response->assertStatus(422);

        $response->assertJson([
            'status' => false,
            'message' => __('messages.promocodes.cannot_activate_own_promocode')
        ]);
    }

    private function createUserSubscription(User $user): void
    {
        Pipeline::send($user)
            ->through([
                GiveSubscription::class
            ])
            ->thenReturn();

        $this->actingAs($user);
    }
}
