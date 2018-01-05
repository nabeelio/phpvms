<?php

use Faker\Generator as Faker;
use Hashids\Hashids;

/**
 * Add any number of airports. Don't really care if they're real or not
 */
$factory->define(App\Models\Airline::class, function (Faker $faker) {
    return [
        #'id' => $faker->unique()->numberBetween(10, 10000),
        'icao' => function (array $apt) use ($faker) {
            $hashids = new Hashids(microtime(), 5);
            $mt = str_replace('.', '', microtime(true));
            return $hashids->encode($mt);
        },
        'iata' => function (array $apt) { return $apt['icao']; },
        'name' => $faker->sentence(3),
        'country' => $faker->country,
        'active' => 1
    ];
});
