<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Acars::class, function (Faker $faker) {
    return [
        'id'          => null,
        'pirep_id'    => null,
        'log'         => $faker->text(100),
        'lat'         => $faker->latitude,
        'lon'         => $faker->longitude,
        'distance'    => $faker->randomFloat(2, 0, 6000),
        'heading'     => $faker->numberBetween(0, 359),
        'altitude'    => $faker->numberBetween(20, 400),
        'vs'          => $faker->numberBetween(-5000, 5000),
        'gs'          => $faker->numberBetween(300, 500),
        'transponder' => $faker->numberBetween(200, 9999),
        'autopilot'   => $faker->text(10),
        'fuel'        => $faker->randomFloat(2, 100, 1000),
        'fuel_flow'   => $faker->randomFloat(2, 100, 1000),
        'sim_time'    => $faker->dateTime('now', 'UTC'),
    ];
});
