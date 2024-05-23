<?php

namespace Tests\Feature\Selection;

use App\Models\Selection\Selection;
use App\Models\User\User;
use App\Pipes\User\GiveSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Pipeline;
use Tests\TestCase;

class DeleteSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_selection(): void
    {
        $this->createUser();
        $selection = Selection::factory()->create();

        $response = $this->deleteJson(route('selection.delete', ['selection' => $selection->id]));

        $response->assertStatus(200);

        $response->assertJson([
            'status' => true,
        ]);

        $this->assertDatabaseMissing('selections', ['id' => $selection->id]);
    }

    private function createUser()
    {
        $user = User::factory()->create();

        Pipeline::send($user)
            ->through([
                GiveSubscription::class
            ])
            ->thenReturn();

        $this->actingAs($user);
        return $user;
    }
}
