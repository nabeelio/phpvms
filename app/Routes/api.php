<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([], function ()
{
    Route::match(['get'], 'status', 'BaseController@status');

    Route::match(['get'], 'airports/{id}', 'AirportController@get');
    Route::match(['get'], 'airports/{id}/lookup', 'AirportController@lookup');

    Route::match(['get'], 'flights/search', 'FlightController@search');
    Route::match(['get'], 'flights/{id}', 'FlightController@get');

    Route::match(['get'], 'pirep/{id}', 'PirepController@get');
    Route::match(['post'], 'pirep/prefile', 'PirepController@prefile');
    Route::match(['post'], 'pirep/{id}/file', 'PirepController@file');

    Route::match(['get'], 'pirep/{id}/acars', 'PirepController@acars_get');
    Route::match(['post'], 'pirep/{id}/acars', 'PirepController@acars_store');

    Route::match(['get'], 'acars', 'AcarsController@index');
    Route::match(['get'], 'acars/geojson', 'AcarsController@geojson');

    # This is the info of the user whose token is in use
    Route::match(['get'], 'user', 'UserController@index');
    #Route::match(['get'], 'user/bids', 'UserController@index');
    Route::match(['get'], 'users/{id}', 'UserController@get');
    Route::match(['get'], 'users/{id}/bids', 'UserController@bids');
});
