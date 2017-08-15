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

Route::group([], function () {

    Route::match(['get'], 'status', 'BaseController@status');

    Route::match(['get'], 'flight/{id}', 'FlightController@get');
    Route::match(['get'], 'flights/search', 'FlightController@search');

    Route::match(['get'], 'pirep/{id}', 'PirepController@get');
});
