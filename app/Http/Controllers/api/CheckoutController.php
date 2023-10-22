<?php

namespace App\Http\Controllers\api;

use App\Classes\checkOutHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Http\Resources\orderResource;
use App\Models\Table;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function checkout(CheckoutRequest $request)
    {


        $checkOutHandler = new checkOutHandler();
        $checkoutStrategy = $checkOutHandler->getCheckoutStrategy($request->method);

        //get reservation info 
        $table = Table::with('orders.user', 'orders.orderDetails')->find($request->table_id);
        if ($table->orders->isEmpty()) {
            throw ValidationException::withMessages([
                'table_id' => 'this table does not have any orders'
            ]);
        }


        $checkAmount = $table->orders->sum('total');
        $total_paid = $table->orders->sum('paid');
        //calculate total amount 
        $amount = $checkoutStrategy->calculateTotal($checkAmount);
        $totalAmount = number_format($amount - $total_paid ,2 , '.' , '');
        //print
        $checkoutStrategy->printInvoice($request->table_id, $totalAmount);
        //retrn response 
        return response()->json([
            "total_amount_to_pay" => $totalAmount,
            "table_no" => $request->table_id,
            "orders" => orderResource::collection($table->orders)
        ], 200);



        // Additional logic for payment and response handling
    }
}
