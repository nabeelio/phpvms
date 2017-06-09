<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return redirect('home');
});


Route::get('/home', 'HomeController@index');


//Auth::routes();

/**
 * Admin routes
 */
Route::group([
    'namespace' => 'Admin',
    'middleware' => 'auth',
    'prefix' => 'admin',
], function () {
    Route::resource('airlines', 'AirlinesController');
    Route::resource('aircraft', 'AircraftController');

//    Route::get('admin/aircrafts', ['as'=> 'admin.aircrafts.index', 'uses' => 'AircraftController@index']);
//    Route::post('admin/aircrafts', ['as'=> 'admin.aircrafts.store', 'uses' => 'AircraftController@store']);
//    Route::get('admin/aircrafts/create', ['as'=> 'admin.aircrafts.create', 'uses' => 'AircraftController@create']);
//    Route::put('admin/aircrafts/{aircrafts}', ['as'=> 'admin.aircrafts.update', 'uses' => 'AircraftController@update']);
//    Route::patch('admin/aircrafts/{aircrafts}', ['as'=> 'admin.aircrafts.update', 'uses' => 'AircraftController@update']);
//    Route::delete('admin/aircrafts/{aircrafts}', ['as'=> 'admin.aircrafts.destroy', 'uses' => 'AircraftController@destroy']);
//    Route::get('admin/aircrafts/{aircrafts}', ['as'=> 'admin.aircrafts.show', 'uses' => 'AircraftController@show']);
//    Route::get('admin/aircrafts/{aircrafts}/edit', ['as'=> 'admin.aircrafts.edit', 'uses' => 'AircraftController@edit']);
});


