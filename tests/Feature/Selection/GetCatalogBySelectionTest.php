<?php

namespace Tests\Feature\Selection;

use App\Models\Catalogs\Property\CatalogProperty;
use App\Models\Catalogs\Property\CatalogPropertyImg;
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

class GetCatalogBySelectionTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

       $this->createAndAuthUser();
    }

    /**
     * @test
     * Test catalog returns paginated response
     * @return void
     */
    public function test_catalog_returns_paginated_data(): void
    {
        $selection = Selection::factory()->create([
            'beds' => ['Studio'],
            'size_from' => 100,
            'size_to' => 200,
        ]);

        $this->createCatalogBySelection($selection);
        $this->createAndAuthUser();

        $response = $this->getJson(route('selection.catalog', $selection));

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
            ])
            ->assertJsonStructure([
                'catalog' => [
                    'data' => [
                        '*' => ['id', 'active_flag', 'property_type', 'price', 'created_at']
                    ],
                    'links' => ['previous_page_url', 'next_page_url'],
                    'meta' => ['total', 'current_page', 'last_page', 'per_page'],
                ],
            ]);
    }

    /**
     * Test catalog returns paginated response and sprted by price
     * @return void
     */
    public function test_catalog_returns_paginated_data_sorted_by_price_ascending()
    {
        $selection = Selection::factory()->create([
            'beds' => ['Studio'],
            'size_from' => 100,
            'size_to' => 200,
        ]);

        $this->createCatalogBySelection($selection);
        $this->createAndAuthUser();

        $response = $this->getJson(
            route('selection.catalog', ['selection' => $selection, 'sort' => ['price' => 'asc']])
        );

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
            ])
            ->assertJsonStructure([
                'catalog' => [
                    'data' => [
                        '*' => ['id', 'active_flag', 'property_type', 'price', 'created_at']
                    ],
                    'links' => ['previous_page_url', 'next_page_url'],
                    'meta' => ['total', 'current_page', 'last_page', 'per_page'],
                ],
            ]);

        $catalogData = $response->json('catalog.data');
        $sortedByPrice = collect($catalogData)->sortBy('price')->values()->toArray();

        $this->assertEquals($catalogData, $sortedByPrice, 'Catalog items are sorted by price ascending.');
    }

    protected function createCatalogBySelection(Selection $selection): void
    {
        CatalogProperty::factory(10)
            ->has(CatalogPropertyImg::factory()->count(3), 'images')
            ->create([
                'active_flag' => true,
                'property_type' => $selection->property_type,
                'price' => $selection->budget_from,
                'deal_type' => $selection->deal_type,
                'completion_type' => $selection->completion,
                'bedrooms' => 'Studio',
                'size_m2' => 150,
                'size_sqft' => 150,
            ]);
    }

    protected function createAndAuthUser(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create(['price' => 0]);

        Pipeline::send($user)
            ->through([
                CreateBalance::class,
                GenerateAndCreatePromocode::class,
                GiveSubscription::class
            ])
            ->thenReturn();

        $this->actingAs($user);
    }
}
