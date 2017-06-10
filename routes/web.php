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

Auth::routes();

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');


Route::group([
    'namespace' => 'Frontend',
    'middleware' => 'auth',
], function () {
    Route::resource('dashboard', 'DashboardController');
});



Route::get('admin/aircraftClasses', ['as'=> 'admin.aircraftClasses.index', 'uses' => 'AircraftClassController@index']);
Route::post('admin/aircraftClasses', ['as'=> 'admin.aircraftClasses.store', 'uses' => 'AircraftClassController@store']);
Route::get('admin/aircraftClasses/create', ['as'=> 'admin.aircraftClasses.create', 'uses' => 'AircraftClassController@create']);
Route::put('admin/aircraftClasses/{aircraftClasses}', ['as'=> 'admin.aircraftClasses.update', 'uses' => 'AircraftClassController@update']);
Route::patch('admin/aircraftClasses/{aircraftClasses}', ['as'=> 'admin.aircraftClasses.update', 'uses' => 'AircraftClassController@update']);
Route::delete('admin/aircraftClasses/{aircraftClasses}', ['as'=> 'admin.aircraftClasses.destroy', 'uses' => 'AircraftClassController@destroy']);
Route::get('admin/aircraftClasses/{aircraftClasses}', ['as'=> 'admin.aircraftClasses.show', 'uses' => 'AircraftClassController@show']);
Route::get('admin/aircraftClasses/{aircraftClasses}/edit', ['as'=> 'admin.aircraftClasses.edit', 'uses' => 'AircraftClassController@edit']);


Route::get('admin/fares', ['as'=> 'admin.fares.index', 'uses' => 'FareController@index']);
Route::post('admin/fares', ['as'=> 'admin.fares.store', 'uses' => 'FareController@store']);
Route::get('admin/fares/create', ['as'=> 'admin.fares.create', 'uses' => 'FareController@create']);
Route::put('admin/fares/{fares}', ['as'=> 'admin.fares.update', 'uses' => 'FareController@update']);
Route::patch('admin/fares/{fares}', ['as'=> 'admin.fares.update', 'uses' => 'FareController@update']);
Route::delete('admin/fares/{fares}', ['as'=> 'admin.fares.destroy', 'uses' => 'FareController@destroy']);
Route::get('admin/fares/{fares}', ['as'=> 'admin.fares.show', 'uses' => 'FareController@show']);
Route::get('admin/fares/{fares}/edit', ['as'=> 'admin.fares.edit', 'uses' => 'FareController@edit']);
