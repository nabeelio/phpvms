<?php

Route::group(['middleware' => [
    'role:admin|user' # leave blank to make this public
]], function() {

    # all your routes are prefixed with the above prefix
    # e.g. yoursite.com/sample
    Route::get('/', 'SampleController@index');

    # This is the admin path. Comment this out if you don't have
    # an admin panel component.
    Route::group([
         'middleware' => ['role:admin'],
     ], function () {
        Route::get('/admin', 'AdminController@index');
    });
});
