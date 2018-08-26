<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Aircraft::class, function (Faker $faker) {
    return [
        'id'          => null,
        'subfleet_id' => function () {
            return factory(App\Models\Subfleet::class)->create()->id;
        },
        'airport_id' => function () {
            return factory(App\Models\Airport::class)->create()->id;
        },
        'iata'         => $faker->unique()->text(5),
        'icao'         => $faker->unique()->text(5),
        'name'         => $faker->text(50),
        'registration' => $faker->unique()->text(10),
        'hex_code'     => \App\Support\ICAO::createHexCode(),
        'zfw'          => $faker->randomFloat(2, 0, 50000),
        'status'       => \App\Models\Enums\AircraftStatus::ACTIVE,
        'state'        => \App\Models\Enums\AircraftState::PARKED,
        'created_at'   => $faker->dateTimeBetween('-1 week', 'now'),
        'updated_at'   => function (array $pirep) {
            return $pirep['created_at'];
        },
    ];
});
