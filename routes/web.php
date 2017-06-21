<?php

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');


/**
 * These are only visible to a logged in user
 */
Route::group([
    'namespace' => 'Frontend', 'prefix' => 'frontend', 'as' => 'frontend.',
    'middleware' => ['role:admin|user'],
], function () {
    Route::resource('dashboard', 'DashboardController');
});

Auth::routes();

require base_path('routes/admin.php');


Route::get('admin/rankings', ['as'=> 'admin.rankings.index', 'uses' => 'RankingController@index']);
Route::post('admin/rankings', ['as'=> 'admin.rankings.store', 'uses' => 'RankingController@store']);
Route::get('admin/rankings/create', ['as'=> 'admin.rankings.create', 'uses' => 'RankingController@create']);
Route::put('admin/rankings/{rankings}', ['as'=> 'admin.rankings.update', 'uses' => 'RankingController@update']);
Route::patch('admin/rankings/{rankings}', ['as'=> 'admin.rankings.update', 'uses' => 'RankingController@update']);
Route::delete('admin/rankings/{rankings}', ['as'=> 'admin.rankings.destroy', 'uses' => 'RankingController@destroy']);
Route::get('admin/rankings/{rankings}', ['as'=> 'admin.rankings.show', 'uses' => 'RankingController@show']);
Route::get('admin/rankings/{rankings}/edit', ['as'=> 'admin.rankings.edit', 'uses' => 'RankingController@edit']);
