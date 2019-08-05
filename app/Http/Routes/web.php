<?php

/**
 * User doesn't need to be logged in for these
 */
Route::group([
    'namespace' => 'Frontend', 'prefix' => '', 'as' => 'frontend.',
], function () {
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('r/{id}', 'PirepController@show')->name('pirep.show.public');
    Route::get('p/{id}', 'ProfileController@show')->name('profile.show.public');

    Route::get('users', 'UserController@index')->name('users.index');
    Route::get('pilots', 'UserController@index')->name('pilots.index');

    Route::get('livemap', 'LiveMapController@index')->name('livemap.index');
});

/*
 * These are only visible to a logged in user
 */
Route::group([
    'namespace'  => 'Frontend', 'prefix' => '', 'as' => 'frontend.',
    'middleware' => ['role:admin|user'],
], function () {
    Route::resource('dashboard', 'DashboardController');

    Route::get('airports/{id}', 'AirportController@show')->name('airports.show');

    // Download a file
    Route::get('downloads', 'DownloadController@index')->name('downloads.index');
    Route::get('downloads/{id}', 'DownloadController@show')->name('downloads.download');

    Route::get('flights/bids', 'FlightController@bids')->name('flights.bids');
    Route::get('flights/search', 'FlightController@search')->name('flights.search');
    Route::resource('flights', 'FlightController');

    Route::get('pireps/fares', 'PirepController@fares');
    Route::resource('pireps', 'PirepController');
    Route::post('pireps/{id}/submit', 'PirepController@submit')->name('pireps.submit');

    Route::get('profile/regen_apikey', 'ProfileController@regen_apikey')
        ->name('profile.regen_apikey');
    Route::resource('profile', 'ProfileController');
});

Auth::routes(['verify' => true]);
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

require app_path('Http/Routes/admin.php');
