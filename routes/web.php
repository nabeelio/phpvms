<?php

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');


/**
 * These are only visible to a logged in user
 */
Route::group([
    'namespace' => 'Frontend', 'prefix' => 'user', 'as' => 'frontend.',
    'middleware' => ['role:admin|user'],
], function () {
    Route::resource('dashboard', 'DashboardController');
});

Auth::routes();
Route::get('/logout', 'Auth\LoginController@logout');

require base_path('routes/admin.php');
