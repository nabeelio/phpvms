<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Subfleet::class, function (Faker $faker) {
    return [
        'id' => $faker->unique()->numberBetween(10, 10000),
        'airline_id' => 1,
        'name' => $faker->unique()->text(50),
        'type' => $faker->unique()->text(7),
    ];
});
