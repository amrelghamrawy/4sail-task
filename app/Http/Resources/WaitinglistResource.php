<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WaitinglistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'customer_id' => (int) $this->customer_id , 
            'capacity_wanted' =>(int) $this->Capacity_wanted,
            'dateTime' =>  $this->created_at->toDateTimeString()
        ];
    }
}
