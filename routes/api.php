<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([

    'middleware' => 'api',
    // 'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::post('register', 'AuthController@register');

     // Password reset link request routes...
     Route::get('password/email', 'Auth\ForgotPasswordController@getEmail');
     Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
 
     // Password reset routes...
     Route::get('password/reset/{token}', 'Auth\ResetPasswordController@getReset');
     Route::post('password/reset', 'Auth\ResetPasswordController@reset');
     Route::resource('consultant', 'ConsultantController');

});