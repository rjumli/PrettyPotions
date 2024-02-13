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
    Route::prefix('01101011 01110010 01100001 01100100')->group(function(){
        Route::prefix('scholars')->group(function(){
            Route::get('/{code}', [App\Http\Controllers\ScholarController::class, '/']);
            Route::post('/', [App\Http\Controllers\ScholarController::class, 'store']);
        });
    });
});
