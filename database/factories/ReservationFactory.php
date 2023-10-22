<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Table ; 
use App\Models\Customer ; 

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
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
            'from_time' => now()->format('Y-m-d H:i:s'),
            'to_time' => now()->addHours(2)->format('Y-m-d H:i:s'),
        ];
    }
}
