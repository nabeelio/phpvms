<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

/**
 * Create a new PIREP
 */
$factory->define(App\Models\Pirep::class, function (Faker $faker) {

    static $raw_data;

    return [
        'id' => null,
        'airline_id' => function () { # OVERRIDE THIS IF NEEDED
            return factory(App\Models\Airline::class)->create()->id;
        },
        'user_id' => function () { # OVERRIDE THIS IF NEEDED
            return factory(App\Models\User::class)->create()->id;
        },
        'aircraft_id' => function () {
            return factory(App\Models\Aircraft::class)->create()->id;
        },
        'flight_number' => function () {
            return factory(App\Models\Flight::class)->create()->flight_number;
        },
        'route_code' => function(array $pirep) {
            //return App\Models\Flight::where(['flight_number' => $pirep['flight_number']])->first()->route_code;
        },
        'route_leg' => function (array $pirep) {
            //return App\Models\Flight::where('flight_number', $pirep['flight_number'])->first()->route_leg;
        },
        'dpt_airport_id' => function () {
            return factory(App\Models\Airport::class)->create()->id;
        },
        'arr_airport_id' => function () {
            return factory(App\Models\Airport::class)->create()->id;
        },
        'level' => $faker->numberBetween(20, 400),
        'distance' => $faker->randomFloat(2),
        'planned_distance' => $faker->randomFloat(2),
        'flight_time' => $faker->randomFloat(2),
        'planned_flight_time' => $faker->randomFloat(2),
        'zfw' => $faker->randomFloat(2),
        'block_fuel' => $faker->randomFloat(2),
        'fuel_used' => $faker->randomFloat(2),
        'route' => $faker->text(200),
        'notes' => $faker->text(200),
        'source' => $faker->randomElement([PirepSource::MANUAL, PirepSource::ACARS]),
        'source_name' => 'Test Factory',
        'state' => PirepState::PENDING,
        'status' => PirepStatus::SCHEDULED,
        'raw_data' => $raw_data ?: $raw_data = json_encode(['key' => 'value']),
        'created_at' => Carbon::now()->toDateTimeString(),
        'updated_at' => function(array $pirep) {
            return $pirep['created_at'];
        },
    ];
});
