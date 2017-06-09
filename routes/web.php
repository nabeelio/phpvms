<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return redirect('home');
});


Route::get('/home', 'HomeController@index');



/**
 * Admin routes
 */

Route::get('/admin', function () {
    return redirect('/admin/airlines');
});

Route::group([
    'namespace' => 'Admin',
    'middleware' => 'auth',
    'prefix' => 'admin',
], function () {
    Route::resource('airlines', 'AirlinesController');
    Route::resource('aircraft', 'AircraftController');
});


