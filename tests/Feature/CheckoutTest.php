<?php

namespace Tests\Feature;

use App\Models\Meal;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function PHPUnit\Framework\assertJson;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_unpricessable_for_wrong_methods()
    {
        $table = Table::factory()->create();
        $method = 'wrong_type';

        $response = $this->getJson("/api/checkout?table_id=$table->id&method=$method");

        $response->assertUnprocessable()
            ->assertInvalid('method');
    }

    public function test_table_with_no_orders()
    {
        $table = Table::factory()->create();
        $method = 'service_only';

        $response = $this->getJson("/api/checkout?table_id=$table->id&method=$method");

        $response->assertUnprocessable()
            ->assertInvalid('table_id');
    }
    public function test__one_order_service_only_method_estimated_correctly()
    {
        $meal = Meal::factory()->create();
        $table = Table::factory()->create();
        $order = Order::factory()->create([
            'table_id' => $table->id,
            'reservation_id' => null
        ]);
        $total_paid = $order->paid;

        $order_details = OrderDetails::factory()->create([
            'order_id' => $order->id,
            'meal_id' => $meal->id
        ]);
        $method = 'service_only';

        $response = $this->getJson("/api/checkout?table_id=$table->id&method=$method");

        $serviceCharge = $order->total * 0.15;
        $total_amount = $order->total  + $serviceCharge;

        $total_amount_to_pay  = number_format($total_amount - $total_paid, 2, '.', '');

        $response->assertOk()
            ->assertJsonPath('table_no', "$table->id")
            ->assertJsonPath('total_amount_to_pay', $total_amount_to_pay)
            ->assertJsonPath('orders.0.id', $order->id);
    }


    public function test_one_order_Tax_and_service_method_estimated_correctly()
    {
        $meal = Meal::factory()->create();
        $table = Table::factory()->create();
        $order = Order::factory()->create([
            'table_id' => $table->id,
            'reservation_id' => null
        ]);
        $order_details = OrderDetails::factory()->create([
            'order_id' => $order->id,
            'meal_id' => $meal->id
        ]);
        $method = 'tax_and_service';

        $total_paid = $order->paid;
        $response = $this->getJson("/api/checkout?table_id=$table->id&method=$method");

        $taxes = $order->total * 0.14;
        $serviceCharge = $order->total * 0.2;

        $total_amount = $order->total + $taxes + $serviceCharge;
        $total_amount_to_pay  = number_format($total_amount - $total_paid, 2, '.', '');

        $response->assertOk()
            ->assertJsonPath('table_no', "$table->id")
            ->assertJsonPath('total_amount_to_pay', $total_amount_to_pay)
            ->assertJsonPath('orders.0.id', $order->id);
    }


    public function test_complex_order_service_only_method_estimated_correctly()
    {
        $meal = Meal::factory()->create();
        $meal2 = Meal::factory()->create();
        $meal3 = Meal::factory()->create();
        $meal4 = Meal::factory()->create();

        $table = Table::factory()->create();
        $order1 = Order::factory()->create([
            'table_id' => $table->id,
            'reservation_id' => null
        ]);
        $order2 = Order::factory()->create([
            'table_id' => $table->id,
            'reservation_id' => null
        ]);
        OrderDetails::factory()->create([
            'order_id' => $order1->id,
            'meal_id' => $meal->id
        ]);
        OrderDetails::factory()->create([
            'order_id' => $order1->id,
            'meal_id' => $meal2->id
        ]);
        OrderDetails::factory()->create([
            'order_id' => $order2->id,
            'meal_id' => $meal3->id
        ]);
        OrderDetails::factory()->create([
            'order_id' => $order2->id,
            'meal_id' => $meal4->id
        ]);
        $method = 'service_only';
        $total_paid_1 = $order1->paid;
        $total_paid_2 = $order2->paid;

        $response = $this->getJson("/api/checkout?table_id=$table->id&method=$method");

        $serviceCharge = $order1->total * 0.15;
        $total_amount_to_pay1 = $order1->total  + $serviceCharge;
        $serviceCharge = $order2->total * 0.15;
        $total_amount_to_pay2 = $order2->total  + $serviceCharge;

        $final_total_amount = number_format(
            (($total_amount_to_pay1 + $total_amount_to_pay2) - ($total_paid_1 + $total_paid_2)),
            2,
            '.',
            ''
        );
        $response->assertOk()
            ->assertJsonPath('table_no', "$table->id")
            ->assertJsonPath('total_amount_to_pay', $final_total_amount)
            ->assertJsonPath('orders.0.id', $order1->id)
            ->assertJsonPath('orders.1.id', $order2->id);
    }
    public function test_complex_order_tax_and_service_method_estimated_correctly()
    {
        $meal = Meal::factory()->create();
        $meal2 = Meal::factory()->create();
        $meal3 = Meal::factory()->create();
        $meal4 = Meal::factory()->create();

        $table = Table::factory()->create();
        $order1 = Order::factory()->create([
            'table_id' => $table->id,
            'reservation_id' => null
        ]);
        $order2 = Order::factory()->create([
            'table_id' => $table->id,
            'reservation_id' => null
        ]);
        OrderDetails::factory()->create([
            'order_id' => $order1->id,
            'meal_id' => $meal->id
        ]);
        OrderDetails::factory()->create([
            'order_id' => $order1->id,
            'meal_id' => $meal2->id
        ]);
        OrderDetails::factory()->create([
            'order_id' => $order2->id,
            'meal_id' => $meal3->id
        ]);
        OrderDetails::factory()->create([
            'order_id' => $order2->id,
            'meal_id' => $meal4->id
        ]);
        $method = 'tax_and_service';
        $total_paid_1 = $order1->paid;
        $total_paid_2 = $order2->paid;

        $response = $this->getJson("/api/checkout?table_id=$table->id&method=$method");

        $taxes = $order1->total * 0.14;
        $serviceCharge = $order1->total * 0.2;
        $total_amount_to_pay1 = $order1->total  + $serviceCharge + $taxes;
        $taxes = $order2->total * 0.14;
        $serviceCharge = $order2->total * 0.2;
        $total_amount_to_pay2 = $order2->total  + $serviceCharge + $taxes;

        $final_total_amount = number_format((($total_amount_to_pay1 + $total_amount_to_pay2) - ($total_paid_1 + $total_paid_2)), 2, '.', '');
        $response->assertOk()
            ->assertJsonPath('table_no', "$table->id")
            ->assertJsonPath('total_amount_to_pay', $final_total_amount)
            ->assertJsonPath('orders.0.id', $order1->id)
            ->assertJsonPath('orders.1.id', $order2->id);
    }
}
