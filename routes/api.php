<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', 'AuthController@register');
Route::post('/login', 'AuthController@login');
Route::post('password/email', 'AuthController@forgot');
Route::post('password/reset', 'AuthController@reset');

Route::middleware('auth:api')->group(function (){
    //user
    Route::get('user', 'UserController@show');
    Route::put('users/{user}', 'UserController@update');

    //admin
    Route::post('user/add-funds', 'AdminController@addFunds');
    Route::delete('users/{user}', 'AdminController@destroy');

    //product
    Route::post('products/store', 'ProductController@store');
    Route::put('products/{product}', 'ProductController@update');
    Route::delete('products/{product}', 'ProductController@destroy');
    Route::get('products/{product}/images', 'ProductController@getImages');
    Route::get('products/{product}/price', 'ProductController@getActualPrice');
    Route::get('products', 'ProductController@index'); //testing eager loading

    //product images
    Route::get('productImages', 'ProductImageController@index');
    Route::post('productImages/store', 'ProductImageController@store');
    Route::post('productImages/storeAll', 'ProductImageController@storeAll');
    Route::post('productImages/{productImage}/update', 'ProductImageController@update');
    //Route::put('productImages/{productImage}', 'ProductImageController@update'); does not work
    Route::delete('productImages/{productImage}', 'ProductImageController@destroy');
    Route::get('productImages/{productImage}', 'ProductImageController@getById');
    
    //product prices
    Route::post('productPrices/store', 'ProductPriceController@store');
    Route::put('productPrices/{productPrice}', 'ProductPriceController@update');
    Route::delete('productPrices/{productPrice}', 'ProductPriceController@destroy');

    //reserevation
    Route::get('reservations', 'ReservationController@index');
    Route::get('my-reservations', 'ReservationController@getByUser');
    Route::post('reservations/store', 'ReservationController@store');
    Route::put('reservations/{reservation}', 'ReservationController@update');
    Route::delete('reservations/{reservation}', 'ReservationController@destroy');

});