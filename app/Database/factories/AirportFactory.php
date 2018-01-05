<?php

use Hashids\Hashids;
use Faker\Generator as Faker;

/**
 * Add any number of airports. Don't really care if they're real or not
 */
$factory->define(App\Models\Airport::class, function (Faker $faker) {

    return [
        'id' => function(array $apt) use ($faker) {
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            $string = '';
            $max = strlen($characters) - 1;
            for ($i = 0; $i < 5; $i++) {
                $string .= $characters[random_int(0, $max)];
            }

            return $string;
            #return $faker->unique()->text(5);
            /*$hashids = new Hashids(microtime(), 5);
            $mt = str_replace('.', '', microtime(true));
            return $hashids->encode($mt);*/
        },
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
