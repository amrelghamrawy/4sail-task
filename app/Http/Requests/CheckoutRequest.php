<?php

namespace App\Http\Requests;

use App\Classes\checkOutHandler;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
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
            "table_id" => ["required", "integer", Rule::exists(Table::class, 'id')],
            "method" => ["required", "in:" . implode(',', $this->getAvailableCheckoutMethods())]
        ];
    }

    public function getAvailableCheckoutMethods()
    {
        return array_keys((new checkOutHandler())->checkoutStrategies);
    }
}
