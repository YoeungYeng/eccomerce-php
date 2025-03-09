<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route::post('/login', 'App\Http\Controllers\AuthController@login');
Route::post('/register', [AuthController::class, 'register'])->name('register.api');
Route::post('/login', [AuthController::class, 'login'])->name('login.api');
// middleware('auth:sanctum')->
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/products', products::class);
});