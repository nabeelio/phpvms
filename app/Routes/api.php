<?php

/**
 * Public routes
 */
Route::group([], function () {
    Route::get('acars', 'AcarsController@index');
    Route::get('pireps/{pirep_id}/acars/geojson', 'PirepController@acars_geojson');

    Route::get('status', 'StatusController@status');
    Route::get('version', 'StatusController@status');
});

/**
 * these need to be authenticated with a user's API key
 */
Route::group(['middleware' => ['api.auth']], function () {
    Route::get('airlines', 'AirlineController@index');
    Route::get('airlines/{id}', 'AirlineController@get');

    Route::get('airports', 'AirportController@index');
    Route::get('airports/hubs', 'AirportController@index_hubs');
    Route::get('airports/{id}', 'AirportController@get');
    Route::get('airports/{id}/lookup', 'AirportController@lookup');

    Route::get('fleet', 'FleetController@index');
    Route::get('fleet/aircraft/{id}', 'FleetController@get_aircraft');

    Route::get('flights', 'FlightController@index');
    Route::get('flights/search', 'FlightController@search');
    Route::get('flights/{id}', 'FlightController@get');
    Route::get('flights/{id}/route', 'FlightController@route');

    Route::get('pireps/{pirep_id}', 'PirepController@get');
    Route::put('pireps/{pirep_id}', 'PirepController@update');

    /*
     * ACARS related
     */
    Route::post('pireps/prefile', 'PirepController@prefile');
    Route::post('pireps/{pirep_id}/update', 'PirepController@update');
    Route::post('pireps/{pirep_id}/file', 'PirepController@file');
    Route::post('pireps/{pirep_id}/comments', 'PirepController@comments_post');
    Route::delete('pireps/{pirep_id}/cancel', 'PirepController@cancel');

    Route::get('pireps/{pirep_id}/finances', 'PirepController@finances_get');
    Route::post('pireps/{pirep_id}/finances/recalculate', 'PirepController@finances_recalculate');

    Route::get('pireps/{pirep_id}/route', 'PirepController@route_get');
    Route::post('pireps/{pirep_id}/route', 'PirepController@route_post');
    Route::delete('pireps/{pirep_id}/route', 'PirepController@route_delete');

    Route::get('pireps/{pirep_id}/comments', 'PirepController@comments_get');

    Route::get('pireps/{pirep_id}/acars/position', 'PirepController@acars_get');
    Route::post('pireps/{pirep_id}/acars/position', 'PirepController@acars_store');
    Route::post('pireps/{pirep_id}/acars/positions', 'PirepController@acars_store');

    Route::post('pireps/{pirep_id}/acars/events', 'PirepController@acars_events');
    Route::post('pireps/{pirep_id}/acars/logs', 'PirepController@acars_logs');

    Route::get('settings', 'SettingsController@index');

    # This is the info of the user whose token is in use
    Route::get('user', 'UserController@index');
    Route::get('user/fleet', 'UserController@fleet');
    Route::get('user/pireps', 'UserController@pireps');

    Route::get('user/bids', 'UserController@bids');
    Route::put('user/bids', 'UserController@bids');
    Route::post('user/bids', 'UserController@bids');
    Route::delete('user/bids', 'UserController@bids');

    Route::get('users/{id}', 'UserController@get');
    Route::get('users/{id}/fleet', 'UserController@fleet');
    Route::get('users/{id}/pireps', 'UserController@pireps');

    Route::get('users/{id}/bids', 'UserController@bids');
    Route::put('users/{id}/bids', 'UserController@bids');
});
