<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SliderController;

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
