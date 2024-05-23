<?php

namespace Tests\Feature\Selection;

use App\Models\Catalogs\Property\CatalogProperty;
use App\Models\Selection\Selection;
use App\Models\User\Plan;
use App\Models\User\User;
use App\Pipes\User\CreateBalance;
use App\Pipes\User\GenerateAndCreatePromocode;
use App\Pipes\User\GiveSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Pipeline;
use Tests\TestCase;

class CreateAdvertTest extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = Plan::factory()->create(['price' => 0]);
        $user = User::factory()->create();
        $this->actingAs($user);

        Pipeline::send($user)
            ->through([
                CreateBalance::class,
                GenerateAndCreatePromocode::class,
                GiveSubscription::class
            ])
            ->thenReturn();

        $this->user = $user;
    }

    /**
     * Test create adverts by selection
     */
    public function test_create_adverts_by_selection(): void
    {
        $catalogItemCount = 10;

        $selection = Selection::factory()->create([
            'user_id' => $this->user->id
        ]);

        $propertyCatalog = CatalogProperty::factory($catalogItemCount)->create();

        $data = [
            'selection_id' => $selection->id,
            'added_catalog_items' => [],
        ];

        foreach ($propertyCatalog as $item) {
            $data['added_catalog_items'][] = [
                'catalog_item_id' => $item->id,
                'catalog_name' => 'propertyfinder'
            ];
        }

        $response = $this->postJson(route('selection.create-adverts'), $data);

        $response->assertStatus(201);

        $response->assertJson([
            'status' => true,
        ]);

        $propertyCatalog->each(function ($item) use ($selection) {
            $this->assertDatabaseHas('adverts', [
                'selection_id' => $selection->id,
                'catalogable_type' => CatalogProperty::class,
                'catalogable_id' => $item->id,
            ]);
        });
    }
}
