<?php

use App\Models\Enums\NavaidType;
use Faker\Generator as Faker;

$factory->define(App\Models\Navdata::class, function (Faker $faker) {
    return [
        'id'   => str_replace(' ', '', str_replace('.', '', $faker->unique()->text(5))),
        'name' => str_replace('.', '', $faker->unique()->word),
        'type' => $faker->randomElement([NavaidType::VOR, NavaidType::NDB]),
        'lat'  => $faker->latitude,
        'lon'  => $faker->longitude,
        'freq' => $faker->randomFloat(2, 100, 1000),
    ];
});
