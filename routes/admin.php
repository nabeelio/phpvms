<?php
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
