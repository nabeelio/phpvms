<?php

/**
 * public routes
 */
Route::group([], function()
{
    Route::match(['get'], 'acars', 'AcarsController@index');

    Route::match(['get'], 'flights/search', 'FlightController@search');
    Route::match(['get'], 'flights/{id}', 'FlightController@get');

    Route::match(['post'], 'pirep/{id}/geojson', 'PirepController@file');

    Route::match(['get'], 'status', 'BaseController@status');
});

/**
 * these need to be authenticated with a user's API key
 */
Route::group(['middleware' => ['api.auth']], function ()
{
    Route::match(['get'], 'airports/{id}', 'AirportController@get');
    Route::match(['get'], 'airports/{id}/lookup', 'AirportController@lookup');

    Route::match(['get'], 'pirep/{id}', 'PirepController@get');
    Route::match(['post'], 'pirep/prefile', 'PirepController@prefile');
    Route::match(['post'], 'pirep/{id}/file', 'PirepController@file');

    Route::match(['get'], 'pirep/{id}/acars', 'PirepController@acars_get');
    Route::match(['post'], 'pirep/{id}/acars', 'PirepController@acars_store');

    # This is the info of the user whose token is in use
    Route::match(['get'], 'user', 'UserController@index');
    #Route::match(['get'], 'user/bids', 'UserController@index');
    Route::match(['get'], 'users/{id}', 'UserController@get');
    Route::match(['get'], 'users/{id}/bids', 'UserController@bids');
});
