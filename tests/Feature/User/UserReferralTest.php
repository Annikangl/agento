<?php

namespace Tests\Feature\User;

use App\Exceptions\Api\Promocode\PromocodeException;
use App\Models\User\User;
use App\UseCases\ReferralService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserReferralTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws PromocodeException
     */
    public function test_user_can_add_a_referral_successfully(): void
    {
        $referrer = User::factory()->create();
        $referral = User::factory()->create();

        app(ReferralService::class)->addReferral($referrer, $referral);

        $this->assertDatabaseHas('user_referrals', [
            'referrer_id' => $referrer->id,
            'referral_id' => $referral->id,
            'level' => 1
        ]);
    }

    public function test_it_prevents_adding_a_referral_that_already_exists()
    {
        $this->expectException(PromocodeException::class);

        $service =  app(ReferralService::class);

        $referrer = User::factory()->create();
        $referral = User::factory()->create();

        $service->addReferral($referrer, $referral);
        $service->addReferral($referrer, $referral);
    }
}
