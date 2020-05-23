<?php

use App\Models\Enums\PirepSource;
use App\Models\Enums\PirepState;
use App\Models\Enums\PirepStatus;
use Carbon\Carbon;
use Faker\Generator as Faker;

/*
 * Create a new PIREP
 */
$factory->define(App\Models\Pirep::class, function (Faker $faker) {
    $airline = factory(\App\Models\Airline::class)->create();
    $flight = factory(\App\Models\Flight::class)->create([
        'airline_id' => $airline->id,
    ]);

    return [
        'id'         => $faker->unique()->numberBetween(10, 10000000),
        'airline_id' => function () use ($airline) {
            return $airline->id;
        },
        'user_id' => function () {
            return factory(\App\Models\User::class)->create()->id;
        },
        'aircraft_id' => function () {
            return factory(\App\Models\Aircraft::class)->create()->id;
        },
        'flight_id' => function () use ($flight) {
            return $flight->id;
        },
        'flight_number' => function () use ($flight) {
            return $flight->flight_number;
        },
        'route_code'     => null,
        'route_leg'      => null,
        'dpt_airport_id' => function () use ($flight) {
            return $flight->dpt_airport_id;
        },
        'arr_airport_id' => function () use ($flight) {
            return $flight->arr_airport_id;
        },
        'level'               => $faker->numberBetween(20, 400),
        'distance'            => $faker->randomFloat(2, 0, 6000),
        'planned_distance'    => $faker->randomFloat(2, 0, 6000),
        'flight_time'         => $faker->numberBetween(60, 360),
        'planned_flight_time' => $faker->numberBetween(60, 360),
        'zfw'                 => $faker->randomFloat(2),
        'block_fuel'          => $faker->randomFloat(2, 0, 1000),
        'fuel_used'           => function (array $pirep) {
            return round($pirep['block_fuel'] * .9, 2); // 90% of the fuel loaded was used
        },
        'block_on_time'  => Carbon::now('UTC'),
        'block_off_time' => function (array $pirep) {
            return $pirep['block_on_time']->subMinutes($pirep['flight_time']);
        },
        'route'        => $faker->text(200),
        'notes'        => $faker->text(200),
        'source'       => $faker->randomElement([PirepSource::MANUAL, PirepSource::ACARS]),
        'source_name'  => 'TestFactory',
        'state'        => PirepState::PENDING,
        'status'       => PirepStatus::SCHEDULED,
        'submitted_at' => Carbon::now('UTC')->toDateTimeString(),
        'created_at'   => Carbon::now('UTC')->toDateTimeString(),
        'updated_at'   => function (array $pirep) {
            return $pirep['created_at'];
        },
    ];
});
