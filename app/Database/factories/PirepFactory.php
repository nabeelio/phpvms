<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

/**
 * Create a new PIREP
 */
$factory->define(App\Models\Pirep::class, function (Faker $faker) {
    return [
        'id'                  => $faker->unique()->numberBetween(10, 10000000),
        'airline_id'          => function () {
            return factory(App\Models\Airline::class)->create()->id;
        },
        'user_id'             => function () {
            return factory(App\Models\User::class)->create()->id;
        },
        'aircraft_id'         => function () {
            return factory(App\Models\Aircraft::class)->create()->id;
        },
        'flight_number'       => function (array $pirep) {
            return factory(App\Models\Flight::class)->create([
                'airline_id' => $pirep['airline_id']
            ])->flight_number;
        },
        'route_code'          => null,
        'route_leg'           => null,
        'dpt_airport_id'      => function () {
            return factory(App\Models\Airport::class)->create()->id;
        },
        'arr_airport_id'      => function () {
            return factory(App\Models\Airport::class)->create()->id;
        },
        'level'               => $faker->numberBetween(20, 400),
        'distance'            => $faker->randomFloat(2, 0, 6000),
        'planned_distance'    => $faker->randomFloat(2, 0, 6000),
        'flight_time'         => $faker->numberBetween(60, 360),
        'planned_flight_time' => $faker->numberBetween(60, 360),
        'zfw'                 => $faker->randomFloat(2),
        'block_fuel'          => $faker->randomFloat(2, 0, 30000),
        'fuel_used'           => $faker->randomFloat(2, 0, 30000),
        'block_on_time'       => Carbon::now('UTC'),
        'block_off_time'      => function (array $pirep) {
            return $pirep['block_on_time']->subMinutes($pirep['flight_time']);
        },
        'route'               => $faker->text(200),
        'notes'               => $faker->text(200),
        'source'              => $faker->randomElement([PirepSource::MANUAL, PirepSource::ACARS]),
        'source_name'         => 'Test Factory',
        'state'               => PirepState::PENDING,
        'status'              => PirepStatus::SCHEDULED,
        'submitted_at'        => Carbon::now('UTC')->toDateTimeString(),
        'created_at'          => Carbon::now('UTC')->toDateTimeString(),
        'updated_at'          => function (array $pirep) {
            return $pirep['created_at'];
        },
    ];
});
