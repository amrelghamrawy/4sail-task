<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_to_time_feild_must_be_after_From_time()
    {
        $table = Table::factory()->create();
        $customer = Customer::factory()->create();

        $body = [
            'customer_id' => $customer->id,
            'table_id' => $table->id,
            'from_time' => now()->addHour()->toDateTimeString(),
            "to_time" => now()->toDateTimeString()
        ];

        $response = $this->postJson('/api/reserve-table', $body);

        $response->assertUnprocessable()
            ->assertInvalid('to_time');
    }
    public function test_from_time_and_to_time_in_dateTime_fotmate()
    {
        $table = Table::factory()->create();
        $customer = Customer::factory()->create();

        $body = [
            'customer_id' => $customer->id,
            'table_id' => $table->id,
            'from_time' => now()->toDateString(),
            "to_time" => now()->addHour()->toDateString()
        ];

        $response = $this->postJson('/api/reserve-table', $body);

        $response->assertUnprocessable()
            ->assertInvalid('from_time')
            ->assertInvalid('to_time');
    }
    public function test_required_fields()
    {
        $response = $this->postJson('/api/reserve-table', []);
        $response->assertUnprocessable()
            ->assertInvalid('to_time')
            ->assertInvalid('customer_id')
            ->assertInvalid('table_id')
            ->assertInvalid('from_time');
    }
    public function test_reservation_on_not_Found_table()
    {
        $table = Table::factory()->create();
        $customer = Customer::factory()->create();

        $body = [
            'customer_id' => $customer->id,
            'table_id' => 6595,
            'from_time' => now()->addHour()->toDateTimeString(),
            "to_time" => now()->toDateTimeString()
        ];

        $response = $this->postJson('/api/reserve-table', $body);

        $response->assertUnprocessable()
            ->assertInvalid('table_id');
    }
    public function test_reservation_on_not_Found_Customer()
    {
        $table = Table::factory()->create();
        $customer = Customer::factory()->create();

        $body = [
            'customer_id' => 96645,
            'table_id' => $table->id,
            'from_time' => now()->toDateTimeString(),
            "to_time" => now()->addHour()->toDateTimeString()
        ];

        $response = $this->postJson('/api/reserve-table', $body);

        $response->assertUnprocessable()
            ->assertInvalid('customer_id');
    }
    public function test_from_date_is_within_another_reservation()
    {
        $table = Table::factory()->create();
        $customer1 = Customer::factory()->create();
        $customer2 = Customer::factory()->create();

        Reservation::factory()->create([
            'customer_id' => $customer1->id,
            'table_id' => $table->id,
            'from_time' => now()->addHours(1)->toDateTimeString(),
            'to_time' => now()->addHours(3)->toDateTimeString(),
        ]);
        $body = [
            'customer_id' => $customer2->id,
            'table_id' => $table->id,
            'from_time' => now()->addHours(2)->toDateTimeString(),
            'to_time' => now()->addHours(4)->toDateTimeString(),
        ];
        
        $response = $this->postJson('/api/reserve-table', $body);

        $response->assertUnprocessable()
            ->assertInvalid('table_id')
            ->assertJsonPath('errors.table_id.0', 'You cannot make a reservation during this time');
    }
    public function testTo_timeIsWithInAnotherReservation()
    {
        $table = Table::factory()->create();
        $customer1 = Customer::factory()->create();
        $customer2 = Customer::factory()->create();

        Reservation::factory()->create([
            'customer_id' => $customer1->id,
            'table_id' => $table->id,
            'from_time' => now()->addHours(1)->toDateTimeString(),
            'to_time' => now()->addHours(3)->toDateTimeString(),
        ]);
        $body = [
            'customer_id' => $customer2->id,
            'table_id' => $table->id,
            'from_time' => now()->toDateTimeString(),
            'to_time' => now()->addHours(2)->toDateTimeString(),
        ];
        
        $response = $this->postJson('/api/reserve-table', $body);

        $response->assertUnprocessable()
            ->assertInvalid('table_id')
            ->assertJsonPath('errors.table_id.0', 'You cannot make a reservation during this time');
    }

    public function test_from_time_and_To_time_with_in_another_reservation()
    {
        $table = Table::factory()->create();
        $customer1 = Customer::factory()->create();
        $customer2 = Customer::factory()->create();

        Reservation::factory()->create([
            'customer_id' => $customer1->id,
            'table_id' => $table->id,
            'from_time' => now()->addHours(1)->toDateTimeString(),
            'to_time' => now()->addHours(4)->toDateTimeString(),
        ]);
        $body = [
            'customer_id' => $customer2->id,
            'table_id' => $table->id,
            'from_time' => now()->addHours(2)->toDateTimeString(),
            'to_time' => now()->addHours(3)->toDateTimeString(),
        ];
        
        $response = $this->postJson('/api/reserve-table', $body);

        $response->assertUnprocessable()
            ->assertInvalid('table_id')
            ->assertJsonPath('errors.table_id.0', 'You cannot make a reservation during this time');
    }

    public function test_from_time_and_To_time_greater_than_another_reservation()
    {
        $table = Table::factory()->create();
        $customer1 = Customer::factory()->create();
        $customer2 = Customer::factory()->create();

        Reservation::factory()->create([
            'customer_id' => $customer1->id,
            'table_id' => $table->id,
            'from_time' => now()->addHours(1)->toDateTimeString(),
            'to_time' => now()->addHours(2)->toDateTimeString(),
        ]);
        $body = [
            'customer_id' => $customer2->id,
            'table_id' => $table->id,
            'from_time' => now()->toDateTimeString(),
            'to_time' => now()->addHours(4)->toDateTimeString(),
        ];
        
        $response = $this->postJson('/api/reserve-table', $body);

        $response->assertUnprocessable()
            ->assertInvalid('table_id')
            ->assertJsonPath('errors.table_id.0', 'You cannot make a reservation during this time');
    }
    public function test_success_reservation_if_all_timing_is_correct()
    {
        $table = Table::factory()->create();
        $customer1 = Customer::factory()->create();
        $customer2 = Customer::factory()->create();

        Reservation::factory()->create([
            'customer_id' => $customer1->id,
            'table_id' => $table->id,
            'from_time' => now(),
            'to_time' => now()->addHours(1),
        ]);
        $body = [
            'customer_id' => $customer2->id,
            'table_id' => $table->id,
            'from_time' => now()->addHours(2)->toDateTimeString(),
            'to_time' => now()->addHours(3)->toDateTimeString(),
        ];
        
        $response = $this->postJson('/api/reserve-table', $body);

        $response->assertCreated()
        ->assertJsonPath('reservation.table.id' , $table->id) 
        ->assertJsonPath('reservation.customer.id' , $customer2->id) ; 
    }
}
