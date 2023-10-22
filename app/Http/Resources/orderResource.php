<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class orderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=> $this->id,
            'reservation_id'=> $this->reservation_id,
            'table_id'=> $this->table_id,
            'waiter_name'=> $this->user->name,
            'total'=> $this->total,
            'paid'=> $this->paid,
            'date'=> $this->date,
            'order_details' => orderDetailsResource::collection($this->orderDetails) 
        ];
    }
}
