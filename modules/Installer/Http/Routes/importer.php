<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'ImporterController@index')->name('index');
Route::post('/config', 'ImporterController@config')->name('config');
Route::post('/run', 'ImporterController@run')->middleware('api')->name('run');

Route::post('/complete', 'ImporterController@complete')->name('complete');
