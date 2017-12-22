<?php

use Faker\Generator as Faker;

/**
 * Add any number of airports. Don't really care if they're real or not
 */
$factory->define(App\Models\Airline::class, function (Faker $faker) {
    return [
        'id' => $faker->unique()->numberBetween(10, 10000),
        'icao' => function(array $apt) { return substr($apt['id'],0, 4); },
        'iata' => function (array $apt) { return $apt['id']; },
        'name' => $faker->sentence(3),
        'country' => $faker->country,
        'active' => 1
    ];
});
