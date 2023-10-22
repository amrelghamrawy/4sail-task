<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\checkAvailabilityRequest;
use App\Http\Requests\reserveTableRequest;
use App\Http\Resources\checkAvailabilityResource;
use App\Http\Resources\WaitinglistResource;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\WaitingList;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TableController extends Controller
{
    public function checkAvailability(checkAvailabilityRequest $request)
    {
        $request->validated();

        $tableExists = Table::where('capacity', '>=', $request->number_of_guests)
            ->whereDoesntHave('reservations', function ($query) use ($request) {
                $query->inDate($request->datetime);
            })
            ->exists();

        $waiting_list = null;
        
        if (!$tableExists) {
            $waiting_list = WaitingList::create([
                'customer_id' => $request->customer_id,
                'Capacity_wanted' => $request->number_of_guests,
            ]);
        }

        return response([
            "is_available_tables" => $tableExists,
            "waitingList" => $waiting_list ? WaitinglistResource::make($waiting_list) : null
        ], 200);
    }
    public function reserveTable(reserveTableRequest $request)
    {

        $request->validated();
        $reservationExists = Reservation::where('table_id', $request->table_id)
            ->betweenDates($request->from_time, $request->to_time)->first();


        if ($reservationExists) {
            throw ValidationException::withMessages([
                'table_id' => 'You cannot make a reservation during this time'
            ]);
        }
        $Reservation = Reservation::create([
            'table_id' => $request->table_id,
            'customer_id' => $request->customer_id,
            'from_time' => $request->from_time,
            'to_time' => $request->to_time
        ]);


        return response(["message" => "your reservation created successfully", 'reservation' => $Reservation->load('table')->load('customer')], 201);
    }
}
