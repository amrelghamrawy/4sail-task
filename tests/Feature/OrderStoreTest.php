<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Meal;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderStoreTest extends TestCase
{
    use RefreshDatabase ; 
    public function test_unAuthinticated_user_cannot_create_order()
    {
        $customer = Customer::factory()->create();
        $table = Table::factory()->create();
        $meal = Meal::factory()->create();
        $meal2 = Meal::factory()->create();
        $reservation = Reservation::factory()->create(["customer_id" => $customer->id, 'table_id' => $table->id]);

        $data = [
            "customer_id" => $customer->id,
            "reservation_id" => $reservation->id,
            'table_id' => $table->id,
            'paid' => 20,
            'date' => now()->toDateString(),
            'meals' => [
                ['id' => $meal->id],
                ['id' => $meal2->id],
            ]
        ];

        $response = $this->postJson('/api/order', $data);

        $response->assertUnauthorized();
    }
    public function test_Authinticated_user_can_create_order()
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();
        $table = Table::factory()->create();
        $meal = Meal::factory()->create();
        $meal2 = Meal::factory()->create();
        $reservation = Reservation::factory()->create(["customer_id" => $customer->id, 'table_id' => $table->id]);

        $data = [
            "customer_id" => $customer->id,
            "reservation_id" => $reservation->id,
            'table_id' => $table->id,
            'paid' => 20,
            'date' => now()->toDateString(),
            'meals' => [
                ['id' => $meal->id],
                ['id' => $meal2->id]
            ]
        ];

        $response = $this->actingAs($user)->postJson('/api/order', $data);
        $response->assertCreated();
    }
    public function test_order_data_persists_successfully()
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();
        $table = Table::factory()->create();
        $meal = Meal::factory()->create();
        $meal2 = Meal::factory()->create();
        $reservation = Reservation::factory()->create(["customer_id" => $customer->id, 'table_id' => $table->id]);

        $data = [
            "customer_id" => $customer->id,
            "reservation_id" => $reservation->id,
            'table_id' => $table->id,
            'paid' => 20,
            'date' => now()->toDateString(),
            'meals' => [
                ['id' => $meal->id],
                ['id' => $meal2->id]
            ]
        ];

        $response = $this->actingAs($user)->postJson('/api/order', $data);

        $response->assertCreated()
            ->assertJsonPath('order.reservation_id', $reservation->id)
            ->assertJsonPath('order.table_id', $table->id)
            ->assertJsonPath('order.customer_id', $customer->id)
            ->assertJsonPath('order.user_id', $user->id)
            ->assertJsonPath('order.paid', '20.00')
            ->assertJsonPath('order.date', now()->toDateString())
            ->assertJsonPath('order.order_details.0.meal_id', $meal->id)
            ->assertJsonPath('order.order_details.1.meal_id', $meal2->id);
    }

    public function test_order_Total_estemated_correctly_after_discout()
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();
        $table = Table::factory()->create();
        $meal = Meal::factory()->create();
        $meal2 = Meal::factory()->create();
        $reservation = Reservation::factory()->create(["customer_id" => $customer->id, 'table_id' => $table->id]);

        $data = [
            "customer_id" => $customer->id,
            "reservation_id" => $reservation->id,
            'table_id' => $table->id,
            'paid' => 20,
            'date' => now()->toDateString(),
            'meals' => [
                ['id' => $meal->id],
                ['id' => $meal2->id]
            ]
        ];
        $mealAmountToPay = number_format($meal->price - ($meal->price * ($meal->discount / 100)), 2, '.', '');
        $meal2AmountToPay = number_format(($meal2->price - ($meal2->price * ($meal2->discount / 100))), 2, '.', '');

        $total = number_format($mealAmountToPay + $meal2AmountToPay , 2 , '.' , '');

        $response = $this->actingAs($user)->postJson('/api/order', $data);
        $response->assertCreated()
            ->assertJsonPath('order.order_details.0.amount_to_pay', "$mealAmountToPay")
            ->assertJsonPath('order.order_details.1.amount_to_pay', "$meal2AmountToPay")
            ->assertJsonPath('order.total', "$total");
    }
}
