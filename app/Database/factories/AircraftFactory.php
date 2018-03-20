<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Aircraft::class, function (Faker $faker) {
    return [
        'id'           => null,
        'subfleet_id'  => function () {
            return factory(App\Models\Subfleet::class)->create()->id;
        },
        'airport_id'   => function () {
            return factory(App\Models\Airport::class)->create()->id;
        },
        'icao'         => $faker->unique()->text(5),
        'name'         => $faker->unique()->text(50),
        'registration' => $faker->unique()->text(10),
        'hex_code'     => \App\Support\ICAO::createHexCode(),
        'status'       => \App\Models\Enums\AircraftStatus::ACTIVE,
        'state'        => \App\Models\Enums\AircraftState::PARKED,
        'created_at'   => $faker->dateTimeBetween('-1 week', 'now'),
        'updated_at'   => function (array $pirep) {
            return $pirep['created_at'];
        },
    ];
});
