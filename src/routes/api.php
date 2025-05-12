<?php

use App\Http\Controllers\Product\GetProductsController;
use Illuminate\Support\Facades\Route;

Route::get('/products', GetProductsController::class);
