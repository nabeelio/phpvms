<?php

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');


Route::group([
    'namespace' => 'Frontend',
    'as' => 'frontend.',
    'middleware' => ['role:admin|user'],
], function () {
    Route::resource('dashboard', 'DashboardController');
});

Auth::routes();

/**
 * Admin Routes
 */

Route::group([
    'namespace' => 'Admin',
    'prefix' => 'admin',
    'as' => 'admin.',
    'middleware' => ['role:admin'],
], function () {
    Route::resource('airports', 'AirportController');
    Route::resource('airlines', 'AirlinesController');
    Route::resource('aircraftclasses', 'AircraftClassController');
    Route::resource('fares', 'FareController');

    Route::resource('aircraft', 'AircraftController');
    Route::match(['get', 'post', 'put', 'delete'],
                 'aircraft/{id}/fares',
                 'AircraftController@fares');

    Route::resource('flights', 'FlightController');
    Route::match(['get', 'post', 'put', 'delete'],
                  'flights/{id}/aircraft',
                  'FlightController@aircraft');

    Route::get('', ['uses' => 'DashboardController@index']);
    Route::get('/', ['uses' => 'DashboardController@index']);
    Route::get('/dashboard', ['uses' => 'DashboardController@index','name' => 'dashboard']);
});
