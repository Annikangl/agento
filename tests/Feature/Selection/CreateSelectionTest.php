<?php

namespace Tests\Feature\Selection;

use App\Models\Selection\Selection;
use App\Models\User\User;
use Database\Factories\Selection\SelectionFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateSelectionTest extends TestCase
{
    public function test_create_selection(): void
    {
        $user = User::factory()->create();

        $selection = Selection::factory()->create([
            'user_id' => $user->id
        ]);

        $this->assertDatabaseHas('selections', [
            'id' => $selection->id,
            'title' => $selection->title,
            'user_id' => $user->id,
        ]);
    }
}
