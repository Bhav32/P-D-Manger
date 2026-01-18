<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DiscountController;
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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user', [AuthController::class, 'user']);
});

// Protected routes
Route::middleware('auth:api')->group(function () {
    // Products API
    Route::apiResource('products', ProductController::class);
    
    // Discounts API
    Route::apiResource('discounts', DiscountController::class);
    Route::get('/discounts/active', [DiscountController::class, 'active']);
    Route::get('/products/{productId}/discounts', [DiscountController::class, 'forProduct']);
});

