<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Table ; 
use App\Models\Customer ; 
use App\Models\User ; 
use App\Models\Reservation; 

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'table_id' => Table::factory() , 
            'customer_id' => Customer::factory(), 
            'reservation_id' => Reservation::factory() , 
            'user_id' => User::factory(), 
            'total' => $this->faker->randomFloat(2 , 100 , 500),
            'paid' => $this->faker->randomFloat(2 , 1 , 99),
            'date' => now()->addHours(2)->format('Y-m-d H:i:s')
        ];
    }
}
