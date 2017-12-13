<?php

use Faker\Generator as Faker;

# Match the list available in tests/data/*.yml

$airlinesAvailable = [1];

/**
 * Create a new PIREP
 */
$factory->define(App\Models\Pirep::class, function (Faker $faker) use ($airlinesAvailable) {

    static $raw_data;

    return [
        'id' => $faker->sha1,
        'airline_id' => $faker->randomElement($airlinesAvailable),
        'user_id' => function () { # OVERRIDE THIS IF NEEDED
            return factory(App\Models\User::class)->create()->id;
        },
        'aircraft_id' => function () {
            return factory(App\Models\Aircraft::class)->create()->id;
        },
        'flight_number' => function () {
            return factory(App\Models\Flight::class)->create()->flight_number;
        },
        'route_code' => function(array $pirep) {
            return App\Models\Flight::where('flight_number', $pirep['flight_number'])->first()->route_code;
        },
        'route_leg' => function (array $pirep) {
            return App\Models\Flight::where('flight_number', $pirep['flight_number'])->first()->route_leg;
        },
        'dpt_airport_id' => function () {
            return factory(App\Models\Airport::class)->create()->id;
        },
        'flight_time' => $faker->randomFloat(2),
        'route' => $faker->text(),
        'notes' => $faker->text(),
        'source' => $faker->randomElement([0, 1]),  # MANUAL/ACARS
        'status' => $faker->randomElement([-1, 0, 1]),  # REJECTED/PENDING/ACCEPTED
        'raw_data' => $raw_data ?: $raw_data = json_encode(['key' => 'value']),
        'created_at' => $faker->dateTimeBetween('-1 week', 'now'),
        'updated_at' => function(array $pirep) {
            return $pirep['created_at'];
        },
    ];
});
