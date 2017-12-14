<?php

Route::get('/', 'HomeController@index')->name('home');

/**
 * User doesn't need to be logged in for these
 */
Route::group([
    'namespace' => 'Frontend', 'prefix' => '', 'as' => 'frontend.'
], function() {
    Route::get('/r/{id}', 'PirepController@show')->name('pirep.show.public');
    Route::get('/p/{id}', 'ProfileController@show')->name('profile.show.public');
});

/**
 * These are only visible to a logged in user
 */
Route::group([
    'namespace' => 'Frontend', 'prefix' => '', 'as' => 'frontend.',
    'middleware' => ['role:admin|user'],
], function () {
    Route::resource('dashboard', 'DashboardController');

    Route::get('flights/search', 'FlightController@search')->name('flights.search');
    Route::match(['post'], '/flights/save', 'FlightController@save')->name('flights.save');
    Route::resource('flights', 'FlightController');

    Route::resource('profile', 'ProfileController');
    Route::resource('pireps', 'PirepController');
});

Auth::routes();
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

require app_path('Routes/admin.php');
