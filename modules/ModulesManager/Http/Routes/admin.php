<?php

# This is the admin path. Comment this out if you don't have an admin panel component.
Route::get('/', 'AdminController@index');
Route::post('/addModule', 'AdminController@addModule')->name('addModule');
Route::post('/deleteModule', 'AdminController@deleteModule')->name('deleteModule');
Route::post('/editModule', 'AdminController@editModule')->name('editModule');
