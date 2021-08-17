<?php

use App\Http\Controllers\ApiController;

Route::get('/drinks', [ApiController::class, 'index']);


Route::post('/drink/{drink}', [ApiController::class, 'store']);
Route::delete('/drink/{record}', [ApiController::class, 'destroy']);
