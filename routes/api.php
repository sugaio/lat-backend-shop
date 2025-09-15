<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\RajaOngkirController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/category/{slug?}', [CategoryController::class, 'show']);
Route::get('/categoryHeader', [CategoryController::class, 'categoryHeader']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/product/{slug?}', [ProductController::class, 'show']);

Route::get('/sliders', [SliderController::class, 'index']);

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::get('/cart/total', [CartController::class, 'getCartTotal']);
    Route::get('/cart/total/weight', [CartController::class, 'getCartTotalWeight']);
    Route::post('/cart/remove/{cart}', [CartController::class, 'removeCart']);

    Route::post('/checkout', [CheckoutController::class, 'store']);

    Route::get('/order', [OrderController::class, 'index']);
    Route::get('/order/{snap_token?}', [OrderController::class, 'show']);
});

Route::post('/notificationHandler', [CheckoutController::class, 'notifcationHandler']);

Route::get('/rajaongkir/provinces', [RajaOngkirController::class, 'getProvinces']);
Route::get('/rajaongkir/cities/{provinceID}', [RajaOngkirController::class, 'getCities']);
Route::get('/rajaongkir/districts/{cityID}', [RajaOngkirController::class, 'getDistricts']);
Route::post('/rajaongkir/checkOngkir', [RajaOngkirController::class, 'checkOngkir']);
