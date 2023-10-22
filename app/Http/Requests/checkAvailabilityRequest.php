<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class checkAvailabilityRequest extends FormRequest
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
            "datetime" =>['required' , 'date_format:Y-m-d H:i:s' , 'after:'.now()] , 
            "number_of_guests" => ["required" , "integer"],
            "customer_id" => ["required" , 'integer' , Rule::exists(Customer::class , 'id')]
        ];
    }
}
