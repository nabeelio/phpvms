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
