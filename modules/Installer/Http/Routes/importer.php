<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'ImporterController@index')->name('index');
Route::post('/config', 'ImporterController@config')->name('config');
Route::get('/run', 'ImporterController@run')->name('run');

Route::get('/complete', 'ImporterController@complete')->name('complete');
