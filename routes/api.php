<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

use App\Http\Controllers\ItemController;

use App\Http\Controllers\API\AuthController;

// Registration Route
Route::post('register', [AuthController::class, 'register']);

// Login Route
Route::post('login', [AuthController::class, 'login']);

// Logout Route
Route::middleware('auth:api')->post('logout', [AuthController::class, 'logout']);

Route::controller(AuthController::class)->group(function(){
    Route::get('user','user');
    Route::post('refresh','refresh');
})->middleware('auth:api');



