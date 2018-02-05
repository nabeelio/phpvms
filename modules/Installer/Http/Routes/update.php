<?php

Route::get('/', 'UpdaterController@index')->name('index');

Route::get('/step1', 'UpdaterController@step1')->name('step1');
Route::post('/step1', 'UpdaterController@step1')->name('step1');

Route::post('/run-migrations', 'UpdaterController@run_migrations')->name('run_migrations');
Route::get('/complete', 'UpdaterController@complete')->name('complete');
