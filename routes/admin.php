<?php
/**
 * Admin Routes
 */

Route::group([
     'namespace' => 'Admin', 'prefix' => 'admin', 'as' => 'admin.',
     'middleware' => ['role:admin'],
 ], function () {
    Route::resource('airports', 'AirportController');
    Route::resource('airlines', 'AirlinesController');
    Route::resource('aircraftclasses', 'AircraftClassController');
    Route::resource('fares', 'FareController');

    # aircraft and fare associations
    Route::resource('aircraft', 'AircraftController');
    Route::match(['get', 'post', 'put', 'delete'], 'aircraft/{id}/fares', 'AircraftController@fares');

    # flights and aircraft associations
    Route::resource('flights', 'FlightController');
    Route::match(['get', 'post', 'put', 'delete'], 'flights/{id}/aircraft', 'FlightController@aircraft');

    # view/update settings
    Route::match(['get'], 'settings', 'SettingsController@index');
    Route::match(['post', 'put'], 'settings', 'SettingsController@update');

    # defaults
    Route::get('', ['uses' => 'DashboardController@index']);
    Route::get('/', ['uses' => 'DashboardController@index']);
    Route::get('/dashboard', ['uses' => 'DashboardController@index', 'name' => 'dashboard']);
});
