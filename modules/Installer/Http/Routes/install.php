<?php

Route::get('/', 'InstallerController@index')->name('index');
Route::get('/step1', 'InstallerController@step1')->name('step1');
Route::get('/step2', 'InstallerController@step2')->name('step2');
Route::get('/step3', 'InstallerController@step3')->name('step3');

Route::post('/dbtest', 'InstallerController@dbtest')->name('dbtest');
Route::post('/dbsetup', 'InstallerController@dbsetup')->name('dbsetup');
