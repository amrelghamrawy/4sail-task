<?php

use App\Http\Controllers\api\CheckoutController;
use App\Http\Controllers\api\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use  App\Http\Controllers\api\TableController;
use App\Http\Controllers\api\MealsController;
use App\Http\Controllers\api\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login' , [LoginController::class , 'login']); 
Route::get("/check-availability" , [TableController::class , 'checkAvailability']) ; 
Route::post("/reserve-table" , [TableController::class , 'reserveTable']) ; 
Route::get("/menu-items" , [MealsController::class , 'index']);
Route::middleware('auth:sanctum')->post("/order" , [OrderController::class , 'store']); 
Route::get("/checkout" , [CheckoutController::class , 'checkout']);
