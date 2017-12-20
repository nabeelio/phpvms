<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Navpoint::class, function (Faker $faker) {
    return [
        'id' => $faker->unique()->numberBetween(10, 100000),
        'name' => $faker->unique()->text(10),
        'title' => $faker->unique()->text(25),
        'airway' => $faker->unique()->text(7),
        'lat' => $faker->latitude,
        'lon' => $faker->longitude,
        'freq' => $faker->randomFloat(2, 100, 1000),
    ];
});
