<?php

namespace Tests\Feature;

use App\Models\Meal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class menuItemsTest extends TestCase
{
    use RefreshDatabase;

    public function test_receve_data_successfully()
    {
        Meal::factory(20)->create();

        $response = $this->getJson('/api/menu-items');

        $response->assertOk()
            ->assertJsonCount(20);
    }
    public function test_data_structure()
    {
        Meal::factory(20)->create();
        $response = $this->getJson('/api/menu-items');
        $response->assertOk()
            ->assertJsonCount(20)
            ->assertJsonStructure(['0' => ['id', 'description', 'available_quantity', 'price', 'discount']]);
    }
    public function test_data_doesnot_contain_the_meals_with_available_quantity_0()
    {
        Meal::factory(20)->create();
        Meal::factory(5)->create(['available_quantity' => 0]);
        $response = $this->getJson('/api/menu-items');
        $response->assertOk()
            ->assertJsonCount(20);
    }
}
