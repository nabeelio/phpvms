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
