<?php

use Faker\Generator as Faker;

/**
 * Add any number of airports. Don't really care if they're real or not
 */
$factory->define(App\Models\Airport::class, function (Faker $faker) {
    return [
        'id' => strtoupper($faker->unique()->text(5)),
        'icao' => function(array $apt) { return $apt['id']; },
        'iata' => function (array $apt) { return $apt['id']; },
        'name' => $faker->sentence(3),
        'country' => $faker->country,
        'tz' => $faker->timezone,
        'lat' => $faker->latitude,
        'lon' => $faker->longitude,
        'fuel_100ll_cost' => $faker->randomFloat(2),
        'fuel_jeta_cost' => $faker->randomFloat(2),
        'fuel_mogas_cost' => $faker->randomFloat(2),
    ];
});
