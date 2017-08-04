<?php

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');

/**
 * These are only visible to a logged in user
 */
Route::group([
    'namespace' => 'Frontend', 'prefix' => '', 'as' => 'frontend.',
    'middleware' => ['role:admin|user'],
], function () {
    Route::resource('dashboard', 'DashboardController');

    Route::resource('flights', 'FlightController');
    Route::match(['get'], 'flights/search', 'FlightController@search');
    Route::match(['post'], 'flights/save', 'FlightController@save');

    Route::resource('profile', 'ProfileController');
});

Auth::routes();
Route::get('/logout', 'Auth\LoginController@logout');

require base_path('routes/admin.php');
