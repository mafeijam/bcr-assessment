<?php

use App\Http\Controllers\DrinkController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DrinkController::class, 'index']);
Route::post('/drink/{drink}', [DrinkController::class, 'store']);
Route::delete('/drink/{record}', [DrinkController::class, 'destroy']);
