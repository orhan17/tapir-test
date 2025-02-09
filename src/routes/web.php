<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Route;

Route::get('/stock', [StockController::class, 'index']);
Route::post('/application', [ApplicationController::class, 'store']);

