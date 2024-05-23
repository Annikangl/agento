<?php

namespace Tests\Feature\Addresses;

use App\Models\User\Plan;
use App\Models\User\User;
use App\Pipes\User\CreateBalance;
use App\Pipes\User\GenerateAndCreatePromocode;
use App\Pipes\User\GiveSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Pipeline;
use Tests\TestCase;

class GetAddressesTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_searrch_addresses(): void
    {
        $endpoint = route('address-search', ['name' => 'Dubai']);
        $this->createAndAuthUser();

        $response = $this->getJson($endpoint);

        $response->assertStatus(200);

        $response->assertJson([
                'status' => true,
                'locations' => []]
        );
    }

    public function test_search_with_empty_name(): void
    {
        $endpoint = route('address-search');
        $this->createAndAuthUser();

        $response = $this->getJson($endpoint);

        $response->assertStatus(422);

        $response->assertJson([
            'status' => false,
            'message' => 'The name field is required.'
        ]);
    }

    public function test_search_with_take_param(): void
    {
        $takes = 25;
        $endpoint = route('address-search', ['name' => 'Dubai', 'take' => $takes]);
        $this->createAndAuthUser();

        $response = $this->getJson($endpoint);

        $response->assertStatus(200);

        $this->assertTrue(count($response->json('locations')) <= $takes);
    }

    private function createAndAuthUser(): void
    {
        $user = User::factory()->create();
        Plan::factory()->create([
            'price' => 0
        ]);

        $this->actingAs($user, 'sanctum');

        Pipeline::send($user)
            ->through([
                CreateBalance::class,
                GenerateAndCreatePromocode::class,
                GiveSubscription::class
            ])
            ->thenReturn();
    }
}
