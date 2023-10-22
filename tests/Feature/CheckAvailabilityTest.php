<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\WaitingList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CheckAvailabilityTest extends TestCase
{

    use RefreshDatabase;
    public function test_availablity_without_parameters()
    {
        $table1 = Table::factory()->create(['capacity' => 5]);
        $table2 = Table::factory()->create(['capacity' => 8]);
        $reservation = Reservation::factory()->create([
            'table_id' => $table2->id,
            'from_time' => now()->toDateTimeString(),
            'to_time' => now()->addHours(2)->toDateTimeString()
        ]);
        $checkdate = now()->addHours(1)->toDateTimeString();
        $checkCapacity = 7;
        $response = $this->getJson("/api/check-availability");
        $response->assertUnprocessable()
            ->assertInvalid('datetime')
            ->assertInvalid('number_of_guests')
            ->assertInvalid('customer_id');
    }
    public function test_availablity_with_invalid_datetime_formate()
    {
        $table1 = Table::factory()->create(['capacity' => 5]);
        $table2 = Table::factory()->create(['capacity' => 8]);
        $customer = Customer::factory()->create();
        $reservation = Reservation::factory()->create([
            'table_id' => $table2->id,
            'from_time' => now()->toDateTimeString(),
            'to_time' => now()->addHours(2)->toDateTimeString()
        ]);
        $checkdate = now()->addHours(1)->toDateString();
        $checkCapacity = 7;
        $response = $this->getJson("/api/check-availability?datetime=$checkdate&number_of_guests=$checkCapacity&customer_id=$customer->id");
        $response->assertUnprocessable()
            ->assertInvalid('datetime');
    }
    public function test_availablity_with_invalid_number_of_guistes_formate()
    {
        $table1 = Table::factory()->create(['capacity' => 5]);
        $table2 = Table::factory()->create(['capacity' => 8]);
        $customer = Customer::factory()->create();
        $reservation = Reservation::factory()->create([
            'table_id' => $table2->id,
            'from_time' => now()->toDateTimeString(),
            'to_time' => now()->addHours(2)->toDateTimeString()
        ]);
        $checkdate = now()->addHours(1)->toDateTimeString();
        $checkCapacity = 7.5;
        $response = $this->getJson("/api/check-availability?datetime=$checkdate&number_of_guests=$checkCapacity&customer_id=$customer->id");
        $response->assertUnprocessable()
            ->assertInvalid('number_of_guests');
    }
    public function test_availablity_if_the_date_time_within_another_reservation()
    {
        $table1 = Table::factory()->create(['capacity' => 5]);
        $table2 = Table::factory()->create(['capacity' => 8]);
        $customer = Customer::factory()->create();
        $reservation = Reservation::factory()->create([
            'table_id' => $table2->id,
            'from_time' => now()->toDateTimeString(),
            'to_time' => now()->addHours(2)->toDateTimeString()
        ]);
        $checkdate = now()->addHours(1)->toDateTimeString();
        $checkCapacity = 7;
        $response = $this->getJson("/api/check-availability?datetime=$checkdate&number_of_guests=$checkCapacity&customer_id=$customer->id");
        $response->assertOk()
            ->assertJsonPath('is_available_tables', false);
    }
    public function test_availablity_if_the_date_time_not_in_another_reservation()
    {
        $table1 = Table::factory()->create(['capacity' => 5]);
        $table2 = Table::factory()->create(['capacity' => 8]);
        $customer = Customer::factory()->create();

        $reservation = Reservation::factory()->create([
            'table_id' => $table2->id,
            'from_time' => now()->toDateTimeString(),
            'to_time' => now()->addHours(2)->toDateTimeString()
        ]);
        $checkdate = now()->addHours(3)->toDateTimeString();
        $checkCapacity = 7;
        $response = $this->getJson("/api/check-availability?datetime=$checkdate&number_of_guests=$checkCapacity&customer_id=$customer->id");
        $response->assertOk()
            ->assertJsonPath('is_available_tables', true);
    }
    public function test_availablity_if_the_date_time_within_another_reservation_but_lower_capacity()
    {
        $table1 = Table::factory()->create(['capacity' => 5]);
        $table2 = Table::factory()->create(['capacity' => 8]);
        $customer = Customer::factory()->create();
        $reservation = Reservation::factory()->create([
            'table_id' => $table2->id,
            'from_time' => now()->toDateTimeString(),
            'to_time' => now()->addHours(2)->toDateTimeString()
        ]);
        $checkdate = now()->addHours(1)->toDateTimeString();
        $checkCapacity = 4;
        $response = $this->getJson("/api/check-availability?datetime=$checkdate&number_of_guests=$checkCapacity&customer_id=$customer->id");
        $response->assertOk()
            ->assertJsonPath('is_available_tables', true);
    }
    public function test_customer_is_inserted_to_waiting_list_if_there_is_no_tables_available()
    {
        $table1 = Table::factory()->create(['capacity' => 5]);
        $table2 = Table::factory()->create(['capacity' => 8]);
        $customer = Customer::factory()->create();
        $reservation = Reservation::factory()->create([
            'table_id' => $table2->id,
            'from_time' => now()->toDateTimeString(),
            'to_time' => now()->addHours(2)->toDateTimeString()
        ]);
        $checkdate = now()->addHours(1)->toDateTimeString();
        $checkCapacity = 7;
        $response = $this->getJson("/api/check-availability?datetime=$checkdate&number_of_guests=$checkCapacity&customer_id=$customer->id");
        $waitingList = WaitingList::first() ; 
        $response->assertOk()
            ->assertJsonPath('waitingList.customer_id' , $customer->id)
            ->assertJsonPath('waitingList.capacity_wanted' , $checkCapacity)
            ->assertJsonPath('is_available_tables', false);
    }
}
