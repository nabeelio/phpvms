<?php

$factory->define(App\Models\AircraftClass::class, function (Faker\Generator $faker) {
    return [
        'id' => 1,
        'code' => 'H',
        'name' => 'Heavy',
        'notes' => 'Heavy aircraft',
    ];
});
