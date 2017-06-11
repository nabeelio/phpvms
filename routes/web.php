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

Auth::routes();

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');


Route::group([
    'namespace' => 'Frontend',
    'middleware' => ['role:admin|user'],
], function () {
    Route::resource('dashboard', 'DashboardController');
});

/**
 * Admin Routes
 */

Route::group([
    'namespace' => 'Admin',
    'middleware' => ['role:admin'],
    'prefix' => 'admin',
], function () {
    Route::get('', ['uses' => 'DashboardController@index']);
    Route::get('/', ['uses' => 'DashboardController@index']);

    Route::resource('airports', 'AirportController');
    Route::resource('airlines', 'AirlinesController');
    Route::resource('aircraft', 'AircraftController');
    Route::resource('aircraftclasses', 'AircraftClassController');
    Route::resource('fares', 'FareController');
});
