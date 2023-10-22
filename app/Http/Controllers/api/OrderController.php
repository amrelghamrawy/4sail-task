<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStoreRequest;
use App\Models\Meal;
use App\Models\Order;
use App\Models\OrderDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function store(OrderStoreRequest $request)
    {
        $request->validated();
        $storedOrder =  DB::transaction(function () use ($request) {
            $order = Order::create([
                'reservation_id' => $request->reservation_id,
                'table_id' => $request->table_id,
                'customer_id' => $request->customer_id,
                'user_id' => auth('sanctum')->id(),
                'paid' => $request->paid ?? null,
                'date' => $request->date
            ]);
            $allMeals = Meal::all();
            $total = 0;
            foreach ($request->meals as $meal) {
                
                $stored_meal = $allMeals->find($meal['id']);
                $amount_to_pay = number_format($stored_meal['price'] - ($stored_meal['price'] * ($stored_meal['discount'] / 100)),2 ,'.' ,'');
                OrderDetails::create([
                    'meal_id' => $meal['id'],
                    'order_id' => $order->id,
                    'amount_to_pay'  => $amount_to_pay
                ]);
                $total += $amount_to_pay;
            }

            $order->total = number_format($total , 2,'.','');
            $order->save();
            return $order->load('OrderDetails');
        });

        return response(["order" => $storedOrder], 201);
    }
}
