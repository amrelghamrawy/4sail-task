<?php

namespace App\Http\Requests;

use App\Models\Customer;
use App\Models\Meal;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "customer_id" => ["required", 'integer', Rule::exists(Customer::class, 'id')],
            "reservation_id" => ["required", 'integer', Rule::exists(Reservation::class, 'id')],
            "table_id" => ["required", 'integer', Rule::exists(Table::class, 'id')],
            'paid' => ["sometimes", "required",  "numeric", 'gt:0'],
            'date' => ["required", "date" , "after_or_equal:".today()], 
            'meals' => ["required" , 'array'] , 
            'meals.*.id' => ["required" , 'integer' , Rule::exists(Meal::class , 'id')] 
        ];
    }
}
