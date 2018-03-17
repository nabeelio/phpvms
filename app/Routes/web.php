<?php


/**
 * User doesn't need to be logged in for these
 */
Route::group([
    'namespace' => 'Frontend', 'prefix' => '', 'as' => 'frontend.'
], function() {
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('r/{id}', 'PirepController@show')->name('pirep.show.public');
    Route::get('p/{id}', 'ProfileController@show')->name('profile.show.public');

    Route::get('users', 'UserController@index')->name('users.show');
    Route::get('pilots', 'UserController@index')->name('users.show');

    Route::get('livemap', 'AcarsController@index')->name('livemap.public');
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
    Route::resource('flights', 'FlightController');

    Route::resource('pireps', 'PirepController');

    Route::get('profile/regen_apikey', 'ProfileController@regen_apikey')
        ->name('profile.regen_apikey');
    Route::resource('profile', 'ProfileController');
});

Auth::routes();
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

require app_path('Routes/admin.php');
