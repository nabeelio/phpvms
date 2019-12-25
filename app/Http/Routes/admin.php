<?php
/**
 * Admin Routes
 */
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'namespace'  => 'Admin',
        'prefix'     => 'admin',
        'as'         => 'admin.',
        'middleware' => ['auth', 'ability:admin,admin-access'],
    ],
    static function () {
        // CRUD for airlines
        Route::resource('airlines', 'AirlinesController');

        // CRUD for roles
        Route::resource('roles', 'RolesController');

        Route::get('airports/export', 'AirportController@export')->name('airports.export');
        Route::match(['get', 'post', 'put'], 'airports/fuel', 'AirportController@fuel');

        Route::match(['get', 'post'], 'airports/import', 'AirportController@import')->name(
            'airports.import'
        );

        Route::match(
            ['get', 'post', 'put', 'delete'],
            'airports/{id}/expenses',
            'AirportController@expenses'
        );

        Route::resource('airports', 'AirportController');

        // Awards
        Route::resource('awards', 'AwardController');

        // aircraft and fare associations
        Route::get('aircraft/export', 'AircraftController@export')->name('aircraft.export');

        Route::match(['get', 'post'], 'aircraft/import', 'AircraftController@import')->name(
            'aircraft.import'
        );

        Route::match(
            ['get', 'post', 'put', 'delete'],
            'aircraft/{id}/expenses',
            'AircraftController@expenses'
        );

        Route::resource('aircraft', 'AircraftController');

        // expenses
        Route::get('expenses/export', 'ExpenseController@export')->name('expenses.export');

        Route::match(['get', 'post'], 'expenses/import', 'ExpenseController@import')->name(
            'expenses.import'
        );

        Route::resource('expenses', 'ExpenseController');

        // fares
        Route::get('fares/export', 'FareController@export')->name('fares.export');

        Route::match(['get', 'post'], 'fares/import', 'FareController@import')->name(
            'fares.import'
        );

        Route::resource('fares', 'FareController');

        // files
        Route::post('files', 'FileController@store')->name('files.store');
        Route::delete('files/{id}', 'FileController@destroy')->name('files.delete');

        // finances
        Route::resource('finances', 'FinanceController');

        // flights and aircraft associations
        Route::get('flights/export', 'FlightController@export')->name('flights.export');

        Route::match(['get', 'post'], 'flights/import', 'FlightController@import')->name(
            'flights.import'
        );

        Route::match(
            ['get', 'post', 'put', 'delete'],
            'flights/{id}/fares',
            'FlightController@fares'
        );

        Route::match(
            ['get', 'post', 'put', 'delete'],
            'flights/{id}/fields',
            'FlightController@field_values'
        );

        Route::match(
            ['get', 'post', 'put', 'delete'],
            'flights/{id}/subfleets',
            'FlightController@subfleets'
        );

        Route::resource('flights', 'FlightController');

        Route::resource('flightfields', 'FlightFieldController');

        // pirep related routes
        Route::get('pireps/fares', 'PirepController@fares');
        Route::get('pireps/pending', 'PirepController@pending');
        Route::resource('pireps', 'PirepController');
        Route::match(['get', 'post', 'delete'], 'pireps/{id}/comments', 'PirepController@comments');
        Route::match(['post', 'put'], 'pireps/{id}/status', 'PirepController@status')->name(
            'pirep.status'
        );

        Route::resource('pirepfields', 'PirepFieldController');

        // rankings
        Route::resource('ranks', 'RankController');
        Route::match(
            ['get', 'post', 'put', 'delete'],
            'ranks/{id}/subfleets',
            'RankController@subfleets'
        );

        // settings
        Route::match(['get'], 'settings', 'SettingsController@index');
        Route::match(['post', 'put'], 'settings', 'SettingsController@update')->name(
            'settings.update'
        );

        // maintenance
        Route::match(['get'], 'maintenance', 'MaintenanceController@index')->name(
            'maintenance.index'
        );
        Route::match(['post'], 'maintenance', 'MaintenanceController@cache')->name(
            'maintenance.cache'
        );

        // subfleet
        Route::get('subfleets/export', 'SubfleetController@export')->name('subfleets.export');
        Route::match(['get', 'post'], 'subfleets/import', 'SubfleetController@import')->name(
            'subfleets.import'
        );

        Route::match(
            ['get', 'post', 'put', 'delete'],
            'subfleets/{id}/expenses',
            'SubfleetController@expenses'
        );

        Route::match(
            ['get', 'post', 'put', 'delete'],
            'subfleets/{id}/fares',
            'SubfleetController@fares'
        );

        Route::match(
            ['get', 'post', 'put', 'delete'],
            'subfleets/{id}/ranks',
            'SubfleetController@ranks'
        );

        Route::resource('subfleets', 'SubfleetController');

        Route::resource('users', 'UserController');
        Route::get(
            'users/{id}/regen_apikey',
            'UserController@regen_apikey'
        )->name('users.regen_apikey');

        // defaults
        Route::get('', ['uses' => 'DashboardController@index'])->middleware('update_pending');
        Route::get('/', ['uses' => 'DashboardController@index'])->middleware('update_pending');

        Route::get('dashboard', ['uses' => 'DashboardController@index', 'name' => 'dashboard']);
        Route::match(
            ['get', 'post', 'delete'],
            'dashboard/news',
            ['uses' => 'DashboardController@news']
        )->name('dashboard.news');
    }
);
