<?php
/**
 * Admin routes
 */

Route::get('/admin', [
    'middleware' => ['role:admin'],
    'uses' => 'DashboardController@index'
]);

Route::group([
    'namespace' => 'Admin',
    'middleware' => ['role:admin'],
    'prefix' => 'admin',
], function () {
    Route::resource('airlines', 'AirlinesController');
    Route::resource('aircraft', 'AircraftController');
    Route::resource('aircraftclasses', 'AircraftClassController');
    Route::resource('fares', 'FareController');
});
