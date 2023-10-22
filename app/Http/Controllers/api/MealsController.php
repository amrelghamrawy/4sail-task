<?php

namespace App\Http\Controllers\api;

use App\Http\Resources\MealsResource;
use App\Http\Controllers\Controller;
use App\Models\Meal;
use Illuminate\Http\Request;

class MealsController extends Controller
{
    public function index(){
        $items = Meal::where('available_quantity' ,  '>' , 0)-> orderBY('price')->get() ; 
        return MealsResource::collection($items) ; 
    }
}
