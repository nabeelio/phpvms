<?php

use Faker\Generator as Faker;
use \App\Models\Enums\NavaidType;

$factory->define(App\Models\Navdata::class, function (Faker $faker) {
    return [
        'id' => str_replace(' ', '', str_replace('.', '', $faker->unique()->text(5))),
        'name' => $faker->unique()->text(10),
        'type' => $faker->randomElement([NavaidType::VOR, NavaidType::NDB]),
        'lat' => $faker->latitude,
        'lon' => $faker->longitude,
        'freq' => $faker->randomFloat(2, 100, 1000),
    ];
});
