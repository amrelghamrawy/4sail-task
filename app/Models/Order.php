<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'total' => 'decimal:2',
        'paid' => 'decimal:2'
    ];
    public function OrderDetails(){
        return $this->hasMany(OrderDetails::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
