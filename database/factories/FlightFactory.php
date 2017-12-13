<?php

use Faker\Generator as Faker;

# Match the list available in tests/data/*.yml

$airlinesAvailable = [1];

$airportsAvailable = [
    'KJFK',
    'KAUS',
    'EGLL',
];

$factory->define(App\Models\Flight::class, function (Faker $faker) use ($airportsAvailable, $airlinesAvailable) {
    return [
        'id' => $faker->sha1,
        'flight_number' => $faker->numberBetween(),
        'airline_id' => $faker->randomElement($airlinesAvailable),
        'dpt_airport_id' => $faker->randomElement($airportsAvailable),
        'arr_airport_id' => $faker->randomElement($airportsAvailable),
        'route' => $faker->text(),
        'dpt_time' => $faker->time(),
        'arr_time' => $faker->time(),
        'flight_time' => $faker->randomFloat(2),
        'has_bid' => false,
        'active' => true,
        'created_at' => $faker->dateTimeBetween('-1 week', 'now'),
    ];
});
