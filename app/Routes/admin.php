<?php
/**
 * Admin Routes
 */

Route::group([
     'namespace' => 'Admin', 'prefix' => 'admin', 'as' => 'admin.',
     'middleware' => ['role:admin'],
 ], function () {
    Route::resource('airlines', 'AirlinesController');

    Route::match(['get', 'put'], 'airports/fuel', 'AirportController@fuel');
    Route::resource('airports', 'AirportController');

    Route::resource('fares', 'FareController');

    # subfleet
    Route::resource('subfleets', 'SubfleetController');
    Route::match(['get', 'post', 'put', 'delete'], 'subfleets/{id}/fares', 'SubfleetController@fares');

    # aircraft and fare associations
    Route::resource('aircraft', 'AircraftController');

    # flights and aircraft associations
    Route::resource('flights', 'FlightController');
    Route::match(['get', 'post', 'put', 'delete'], 'flights/{id}/fares', 'FlightController@fares');
    Route::match(['get', 'post', 'put', 'delete'], 'flights/{id}/fields', 'FlightController@fields');
    Route::match(['get', 'post', 'put', 'delete'], 'flights/{id}/subfleets', 'FlightController@subfleets');

    # rankings
    Route::resource('ranks', 'RankController');
    Route::match(['get', 'post', 'put', 'delete'], 'ranks/{id}/subfleets', 'RankController@subfleets');

    # view/update settings
    Route::match(['get'], 'settings', 'SettingsController@index');
    Route::match(['post', 'put'], 'settings', 'SettingsController@update')->name('settings.update');

    # pirep related routes
    Route::resource('pireps', 'PirepController');
    Route::match(['get'], 'pireps/pending', 'PirepController@pending');
    Route::match(['get', 'post', 'delete'], 'pireps/{id}/comments', 'PirepController@comments');
    Route::match(['post', 'put'], 'pireps/{id}/status', 'PirepController@status')->name('pirep.status');

    Route::resource('pirepfields', 'PirepFieldController');

    Route::resource('users', 'UserController');

    # defaults
    Route::get('', ['uses' => 'DashboardController@index']);
    Route::get('/', ['uses' => 'DashboardController@index']);
    Route::get('/dashboard', ['uses' => 'DashboardController@index', 'name' => 'dashboard']);
});
