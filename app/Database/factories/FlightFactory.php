<?php

use Faker\Generator as Faker;

# Match the list available in tests/data/*.yml

$airlinesAvailable = [1];

$factory->define(App\Models\Flight::class, function (Faker $faker) use ($airlinesAvailable) {
    return [
        'id' => substr($faker->unique()->sha1, 28, 12),
        'airline_id' => $faker->randomElement($airlinesAvailable),
        'flight_number' => $faker->unique()->text(10),
        'route_code' => $faker->randomElement(['', $faker->text(5)]),
        'route_leg' => $faker->randomElement(['', $faker->text(5)]),
        'dpt_airport_id' => function() {
            return factory(App\Models\Airport::class)->create()->id;
        },
        'arr_airport_id' => function () {
            return factory(App\Models\Airport::class)->create()->id;
        },
        'alt_airport_id' => function () {
            return factory(App\Models\Airport::class)->create()->id;
        },
        'route' => $faker->randomElement(['', $faker->text(5)]),
        'dpt_time' => $faker->time(),
        'arr_time' => $faker->time(),
        'flight_time' => $faker->randomFloat(2),
        'has_bid' => false,
        'active' => true,
        'created_at' => $faker->dateTimeBetween('-1 week', 'now'),
        'updated_at' => function (array $pirep) {
            return $pirep['created_at'];
        },
    ];
});
