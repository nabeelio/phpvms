<?php

/**
 * public routes
 */
Route::group([], function()
{
    Route::get('acars', 'AcarsController@index');

    Route::get('flights/search', 'FlightController@search');
    Route::get('flights/{id}', 'FlightController@get');

    Route::get('pireps/{id}/acars', 'PirepController@acars_get');
    Route::get('pireps/{id}/geojson', 'PirepController@acars_get');

    Route::get('status', 'BaseController@status');
});

/**
 * these need to be authenticated with a user's API key
 */
Route::group(['middleware' => ['api.auth']], function ()
{
    Route::get('airports/{id}', 'AirportController@get');
    Route::get('airports/{id}/lookup', 'AirportController@lookup');

    Route::get('pireps/{id}', 'PirepController@get');
    Route::post('pireps/prefile', 'PirepController@prefile');
    Route::post('pireps/{id}/file', 'PirepController@file');

    Route::post('pireps/{id}/acars', 'PirepController@acars_store');

    # This is the info of the user whose token is in use
    Route::get('user', 'UserController@index');
    Route::get('users/{id}', 'UserController@get');
    Route::get('users/{id}/bids', 'UserController@bids');
});
