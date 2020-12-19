<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'auth'

], function () {

    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('profile', 'AuthController@profile');

});

Route::apiResource('product', 'ProductController');
