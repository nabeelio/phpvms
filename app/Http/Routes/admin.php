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
        Route::resource('airlines', 'AirlinesController')->middleware('ability:admin,airlines');

        // CRUD for roles
        Route::resource('roles', 'RolesController')->middleware('role:admin');

        Route::get('airports/export', 'AirportController@export')
            ->name('airports.export')
            ->middleware('ability:admin,airports');

        Route::match(['get', 'post', 'put'], 'airports/fuel', 'AirportController@fuel')
            ->middleware('ability:admin,airports');

        Route::match(['get', 'post'], 'airports/import', 'AirportController@import')
            ->name('airports.import')->middleware('ability:admin,airports');

        Route::match(
            ['get', 'post', 'put', 'delete'],
            'airports/{id}/expenses',
            'AirportController@expenses'
        )->middleware('ability:admin,airports');

        Route::resource('airports', 'AirportController')->middleware('ability:admin,airports');

        // Awards
        Route::resource('awards', 'AwardController')->middleware('ability:admin,awards');

        // aircraft and fare associations
        Route::get('aircraft/export', 'AircraftController@export')
            ->name('aircraft.export')
            ->middleware('ability:admin,aircraft');

        Route::match(['get', 'post'], 'aircraft/import', 'AircraftController@import')
            ->name('aircraft.import')->middleware('ability:admin,aircraft');

        Route::match(
            ['get', 'post', 'put', 'delete'],
            'aircraft/{id}/expenses',
            'AircraftController@expenses'
        )->middleware('ability:admin,aircraft');

        Route::resource('aircraft', 'AircraftController')->middleware('ability:admin,aircraft');

        // expenses
        Route::get('expenses/export', 'ExpenseController@export')
            ->name('expenses.export')
            ->middleware('ability:admin,finances');

        Route::match(['get', 'post'], 'expenses/import', 'ExpenseController@import')
            ->name('expenses.import')
            ->middleware('ability:admin,finances');

        Route::resource('expenses', 'ExpenseController')->middleware('ability:admin,finances');

        // fares
        Route::get('fares/export', 'FareController@export')
            ->name('fares.export')
            ->middleware('ability:admin,finances');

        Route::match(['get', 'post'], 'fares/import', 'FareController@import')
            ->name('fares.import')->middleware('ability:admin,finances');

        Route::resource('fares', 'FareController')->middleware('ability:admin,finances');

        // files
        Route::post('files', 'FileController@store')
            ->name('files.store')
            ->middleware('ability:admin,files');

        Route::delete('files/{id}', 'FileController@destroy')
            ->name('files.delete')
            ->middleware('ability:admin,files');

        // finances
        Route::resource('finances', 'FinanceController')->middleware('ability:admin,finances');

        // flights and aircraft associations
        Route::get('flights/export', 'FlightController@export')
            ->name('flights.export')
            ->middleware('ability:admin,flights');

        Route::match(['get', 'post'], 'flights/import', 'FlightController@import')
            ->name('flights.import')
            ->middleware('ability:admin,flights');

        Route::match(
            ['get', 'post', 'put', 'delete'],
            'flights/{id}/fares',
            'FlightController@fares'
        )->middleware('ability:admin,flights');

        Route::match(
            ['get', 'post', 'put', 'delete'],
            'flights/{id}/fields',
            'FlightController@field_values'
        )->middleware('ability:admin,flights');

        Route::match(
            ['get', 'post', 'put', 'delete'],
            'flights/{id}/subfleets',
            'FlightController@subfleets'
        )->middleware('ability:admin,flights');

        Route::resource('flights', 'FlightController')
            ->middleware('ability:admin,flights');

        Route::resource('flightfields', 'FlightFieldController')
            ->middleware('ability:admin,flights');

        // pirep related routes
        Route::get('pireps/fares', 'PirepController@fares')->middleware('ability:admin,pireps');
        Route::get('pireps/pending', 'PirepController@pending')->middleware('ability:admin,pireps');
        Route::resource('pireps', 'PirepController')->middleware('ability:admin,pireps');

        Route::match(['get', 'post', 'delete'], 'pireps/{id}/comments', 'PirepController@comments')
            ->middleware('ability:admin,pireps');

        Route::match(['post', 'put'], 'pireps/{id}/status', 'PirepController@status')
            ->name('pirep.status')
            ->middleware('ability:admin,pireps');

        Route::resource('pirepfields', 'PirepFieldController')
            ->middleware('ability:admin,pireps');

        // rankings
        Route::resource('ranks', 'RankController')->middleware('ability:admin,ranks');
        Route::match(
            ['get', 'post', 'put', 'delete'],
            'ranks/{id}/subfleets',
            'RankController@subfleets'
        )->middleware('ability:admin,ranks');

        // settings
        Route::match(['get'], 'settings', 'SettingsController@index')
            ->middleware('ability:admin,settings');

        Route::match(['post', 'put'], 'settings', 'SettingsController@update')
            ->name('settings.update')
            ->middleware('ability:admin,settings');

        // maintenance
        Route::match(['get'], 'maintenance', 'MaintenanceController@index')
            ->name('maintenance.index')
            ->middleware('ability:admin,maintenance');

        Route::match(['post'], 'maintenance', 'MaintenanceController@cache')
            ->name('maintenance.cache')
            ->middleware('ability:admin,maintenance');

        // subfleet
        Route::get('subfleets/export', 'SubfleetController@export')
            ->name('subfleets.export')
            ->middleware('ability:admin,fleet');

        Route::match(['get', 'post'], 'subfleets/import', 'SubfleetController@import')
            ->name('subfleets.import')
            ->middleware('ability:admin,fleet');

        Route::match(
            ['get', 'post', 'put', 'delete'],
            'subfleets/{id}/expenses',
            'SubfleetController@expenses'
        )->middleware('ability:admin,fleet');

        Route::match(
            ['get', 'post', 'put', 'delete'],
            'subfleets/{id}/fares',
            'SubfleetController@fares'
        )->middleware('ability:admin,fleet');

        Route::match(
            ['get', 'post', 'put', 'delete'],
            'subfleets/{id}/ranks',
            'SubfleetController@ranks'
        )->middleware('ability:admin,fleet');

        Route::resource('subfleets', 'SubfleetController')->middleware('ability:admin,fleet');

        Route::resource('users', 'UserController')->middleware('ability:admin,users');
        Route::get(
            'users/{id}/regen_apikey',
            'UserController@regen_apikey'
        )->name('users.regen_apikey')->middleware('ability:admin,users');

        // defaults
        Route::get('', ['uses' => 'DashboardController@index'])
            ->middleware('update_pending', 'ability:admin,admin-access');

        Route::get('/', ['uses' => 'DashboardController@index'])
            ->middleware('update_pending', 'ability:admin,admin-access');

        Route::get('dashboard', ['uses' => 'DashboardController@index', 'name' => 'dashboard'])
            ->middleware('update_pending', 'ability:admin,admin-access');

        Route::match(
            ['get', 'post', 'delete'],
            'dashboard/news',
            ['uses' => 'DashboardController@news']
        )->name('dashboard.news')->middleware('update_pending', 'ability:admin,admin-access');
    }
);
