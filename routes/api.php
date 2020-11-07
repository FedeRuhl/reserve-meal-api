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

Route::post('/register', 'AuthController@register');
Route::post('/login', 'AuthController@login');

Route::middleware('auth:api')->group(function (){
    //user
    Route::get('user', 'UserController@show');
    Route::post('user', 'UserController@update');

    //admin
    Route::post('user/{user}/funds', 'AdminController@addFunds');

    //product
    Route::post('products/store', 'ProductController@store');
    Route::post('products/{product}/update', 'ProductController@update');
    Route::get('products/{product}/images', 'ProductController@getImages');
    Route::get('products/{product}/price', 'ProductController@getActualPrice');
    Route::get('products', 'ProductController@index'); //testing eager loading

    //product images
    Route::get('productImages', 'ProductImageController@index');
    Route::post('productImages/store', 'ProductImageController@store');
    Route::post('productImages/{productImage}/update', 'ProductImageController@update');
    
    //product prices
    Route::post('productPrices/store', 'ProductPriceController@store');
    Route::post('productPrices/{productPrice}/update', 'ProductPriceController@update');

    //reserevation
    Route::post('reservations/store', 'ReservationController@store');

});