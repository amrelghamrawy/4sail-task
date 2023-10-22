<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function table()
    {
        return $this->belongsTo(Table::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function orders(){
        return $this->hasMany(Order::class) ;
    }
    public function scopeBetweenDates($query, $from, $to)
    {
        $query->where(function ($query) use ($to, $from) {
            $query
                ->whereBetween('from_time', [$from, $to])
                ->orWhereBetween('to_time', [$from, $to])
                ->orWhere(function ($query) use ($to, $from) {
                    $query
                        ->where('from_time', '<', $from)
                        ->where('to_time', '>', $to);
                });
        });
    }
    public function scopeInDate($query, $datetime)
    {
        $query->where(function ($query) use ($datetime) {
            $query->where('from_time', '<=', $datetime)
                ->where('to_time', '>=', $datetime);
        });
    }
}
