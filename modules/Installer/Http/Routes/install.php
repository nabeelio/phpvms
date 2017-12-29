<?php

Route::get('/', 'InstallerController@index')->name('index');
Route::post('/dbtest', 'InstallerController@dbtest')->name('dbtest');

Route::get('/step1', 'InstallerController@step1')->name('step1');
Route::post('/step1', 'InstallerController@step1')->name('step1');

Route::get('/step2', 'InstallerController@step2')->name('step2');
Route::post('/envsetup', 'InstallerController@envsetup')->name('envsetup');
Route::get('/dbsetup', 'InstallerController@dbsetup')->name('dbsetup');

Route::get('/step3', 'InstallerController@step3')->name('step3');
Route::post('/usersetup', 'InstallerController@usersetup')->name('usersetup');

Route::get('/complete', 'InstallerController@complete')->name('complete');
