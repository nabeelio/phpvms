<?php

Route::group(['middleware' => []], function() {

    Route::get('/', 'InstallerController@index');

});
