<?php

use Faker\Generator as Faker;

/**
 * Add any number of airports. Don't really care if they're real or not
 */
$factory->define(App\Models\Airport::class, function (Faker $faker) {
    return [
        'id'                   => function () {
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            $string = '';
            $max = strlen($characters) - 1;
            for ($i = 0; $i < 5; $i++) {
                $string .= $characters[random_int(0, $max)];
            }

            return $string;
        },
        'icao'                 => function (array $apt) {
            return $apt['id'];
        },
        'iata'                 => function (array $apt) {
            return $apt['id'];
        },
        'name'                 => $faker->sentence(3),
        'country'              => $faker->country,
        'timezone'             => $faker->timezone,
        'lat'                  => $faker->latitude,
        'lon'                  => $faker->longitude,
        'ground_handling_cost' => $faker->randomFloat(2, 0, 500),
        'fuel_100ll_cost'      => $faker->randomFloat(2, 0, 100),
        'fuel_jeta_cost'       => $faker->randomFloat(2, 0, 100),
        'fuel_mogas_cost'      => $faker->randomFloat(2, 0, 100),
    ];
});
