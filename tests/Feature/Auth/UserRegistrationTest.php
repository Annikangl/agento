<?php

namespace Tests\Feature\Auth;

use App\Models\User\Plan;
use App\Models\User\Promocode;
use App\Models\User\User;
use App\Models\User\VerificationCode;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        Plan::factory()->create([
            'price' => 0,
            'name' => 'Free',
        ]);
    }

    /**
     * Test if user can  registration
     * @return void
     * @throws Exception
     */
    public function test_user_can_register(): void
    {
        $userData = $this->getUserData();

        $verificationCode = VerificationCode::createVerificationCode($userData->get('email'));

        $userData->put('verification_code', $verificationCode->code);

        $response = $this->json('POST', route('auth.register'), $userData->toArray());

        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJson([
            'status' => true,
            'token' => $response->json('token')
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $userData->get('email'),
            'name' => $userData->get('name'),
        ]);
    }

    /**
     * @throws Exception
     */
    public function test_user_has_trial_subscribtion_after_registration()
    {
        $userData = $this->getUserData();

        $verificationCode = VerificationCode::createVerificationCode($userData->get('email'));

        $userData->put('verification_code', $verificationCode->code);

        $response = $this->json('POST', route('auth.register'), $userData->toArray());

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $response->json('user')['id'],
            'event_type' => 'TRIAL'
        ]);
    }

    /**
     * @throws Exception
     */
    public function test_user_can_register_with_promocode(): void
    {
        $userData = $this->getUserData();
        $referal = User::factory()->create();
        $promocode = Promocode::factory()->create([
            'user_id' => $referal->id
        ]);

        $verificationCode = VerificationCode::createVerificationCode($userData->get('email'));

        $userData->put('verification_code', $verificationCode->code);
        $userData->put('promocode', $promocode->code);

        $response = $this->json('POST', route('auth.register'), $userData->toArray());

        $response->assertStatus(Response::HTTP_CREATED);

        $this->assertEquals(
            $referal->id,
            Promocode::where('code', $promocode->code)->first()->user_id,
            'The promocode is not related to the user');
    }

    /**
     * Test if user can`t registration with invalid email
     * @return void
     * @throws Exception
     */
    public function test_user_cant_register_with_invalid_email(): void
    {
        $userData = $this->getUserData('not_an_email');

        $response = $this->json('POST', route('auth.register'), $userData->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson([
            'status' => false,
        ]);
    }

    /**
     * Test if user can`t registration witout verification code
     * @return void
     * @throws Exception
     */
    public function test_user_cant_register_without_verification_code(): void
    {
        $userData = $this->getUserData();

        $response = $this->json(
            'POST',
            route('auth.register'),
            $userData->toArray()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJson([
            'status' => false,
        ]);

        $this->assertDatabaseEmpty('users');
    }

    /**
     * Test register a new user and check if user has empty balance
     * @return void
     * @throws Exception
     */
    public function test_user_has_empty_balance(): void
    {
        $userData = $this->getUserData();

        $verificationCode = VerificationCode::createVerificationCode($userData->get('email'));

        $userData->put('verification_code', $verificationCode->code);

        $response = $this->json(
            'POST',
            route('auth.register'),
            $userData->toArray()
        );

        $user = $response->json('user');

        $this->assertDatabaseHas('balances', [
            'user_id' => $user['id'],
            'amount' => 0,
        ]);
    }

    /**
     * Test register a new user and check if user has one unique promocode
     * @return void
     * @throws Exception
     */
    public function test_user_has_promocode(): void
    {
        $userData = $this->getUserData();

        $verificationCode = VerificationCode::createVerificationCode($userData->get('email'));

        $userData->put('verification_code', $verificationCode->code);

        $response = $this->json(
            'POST',
            route('auth.register'),
            $userData->toArray()
        );

        $user = $response->json('user');

        $this->assertDatabaseHas('promocodes', [
            'user_id' => $user['id'],
        ]);

        $promocode = Promocode::where('user_id', $user['id'])->first();

        $this->assertEquals(
            1,
            Promocode::where('code',
                $promocode->code)->count(),
            'The promocode is not unique'
        );
    }


    private function getUserData(?string $email = null): Collection
    {
        return collect([
            'name' => $this->faker->userName(),
            'country' => 'uae',
            'email' => !$email ? $this->faker->email() : $email,
            'phone' => '+77020012233',
            'password' => 'secretPassword',
            'password_confirmation' => 'secretPassword',
            'fcm_token' => $this->faker->randomElement([
                $this->faker->sha256(),
                null
            ]),
            'device_name' => $this->faker->userAgent()
        ]);
    }
}
