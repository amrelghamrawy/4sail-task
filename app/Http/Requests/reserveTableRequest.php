<?php

namespace App\Http\Requests;

use App\Models\Customer;
use App\Models\Table;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class reserveTableRequest extends FormRequest
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
            "customer_id" => ['required' , 'integer' ,Rule::exists(Customer::class, 'id'),] , 
            "table_id" => ['required' , 'integer' , Rule::exists(Table::class, 'id'),] , 
            "from_time" => ['required' , 'date_format:Y-m-d H:i:s' , 'after_or_equal:'.now() ] , 
            "to_time" => ['required' , 'date_format:Y-m-d H:i:s' , 'after_or_equal:from_time'  , 'after:'.now()] ,
        ];
    }
}
