<?php

use App\Models\Category;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserController;

Route::get('/', function () {
    return view('auth.login');
});

Route::prefix('admin')->middleware('auth')->name('admin.')->group(function (){
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::resource('/category', CategoryController::class, ['except' => 'show']);
    Route::resource('/product', ProductController::class, ['except' => 'show']);
    Route::resource('/order', OrderController::class, ['only' => ['index','show']]);
    Route::resource('/customer', CustomerController::class, ['only' => 'index']);
    Route::resource('/slider', SliderController::class, ['only' => ['index', 'store', 'destroy']]);
    Route::resource('/profile', ProfileController::class, ['only' => 'index']);
    Route::resource('/user', UserController::class, ['except' => 'show']);
});
