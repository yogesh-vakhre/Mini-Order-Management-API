<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['middleware' => ['auth:sanctum', 'throttle:10,1']], function () {
    Route::resource('products', 'App\Http\Controllers\ProductController')->middleware('admin');
    Route::resource('orders', 'App\Http\Controllers\OrderController');
    Route::post('/auth/logout', 'App\Http\Controllers\AuthController@logout');
    Route::get('/auth/user', 'App\Http\Controllers\AuthController@me');
    Route::put('/cancel-order-user/{bookingId}', 'App\Http\Controllers\AuthController@cancelOrder');
    Route::get('/view-all-order-user', 'App\Http\Controllers\AuthController@viewAllOrder');
});

Route::post('/auth/register', 'App\Http\Controllers\AuthController@register');
Route::post('/auth/login', 'App\Http\Controllers\AuthController@login');
