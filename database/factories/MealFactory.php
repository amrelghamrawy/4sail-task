<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meal>
 */
class MealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "price" => $this->faker->randomFloat(2 , 50  , 200) , 
            'description' => $this->faker->sentence() , 
            'available_quantity' => $this->faker->numberBetween(100, 500) , 
            'discount' => $this->faker->randomElement([0, 15 , 25 , 50])
        ];
    }
}
