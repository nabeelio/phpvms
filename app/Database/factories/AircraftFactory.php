<?php

use App\Models\Airport;
use App\Models\Enums\AircraftState;
use App\Models\Enums\AircraftStatus;
use App\Models\Subfleet;
use App\Support\ICAO;
use Faker\Generator as Faker;

$factory->define(App\Models\Aircraft::class, function (Faker $faker) {
    return [
        'id'          => null,
        'subfleet_id' => function () {
            return factory(Subfleet::class)->create()->id;
        },
        'airport_id' => function () {
            return factory(Airport::class)->create()->id;
        },
        'iata'         => $faker->unique()->text(5),
        'icao'         => $faker->unique()->text(5),
        'name'         => $faker->text(50),
        'registration' => $faker->unique()->text(10),
        'hex_code'     => ICAO::createHexCode(),
        'mtow'         => $faker->randomFloat(2, 0, 50000),
        'zfw'          => $faker->randomFloat(2, 0, 50000),
        'status'       => AircraftStatus::ACTIVE,
        'state'        => AircraftState::PARKED,
        'created_at'   => $faker->dateTimeBetween('-1 week', 'now'),
        'updated_at'   => function (array $pirep) {
            return $pirep['created_at'];
        },
    ];
});
