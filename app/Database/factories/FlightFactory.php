<?php
/**
 * Create flights
 */
use Faker\Generator as Faker;

$factory->define(App\Models\Flight::class, function (Faker $faker) {
    return [
        'id'         => $faker->unique()->numberBetween(10, 10000000),
        'airline_id' => function () {
            return factory(\App\Models\Airline::class)->create()->id;
        },
        'flight_number'  => $faker->unique()->numberBetween(10, 1000000),
        'route_code'     => $faker->randomElement(['', $faker->text(5)]),
        'route_leg'      => $faker->randomElement(['', $faker->numberBetween(0, 1000)]),
        'dpt_airport_id' => function () {
            return factory(\App\Models\Airport::class)->create()->id;
        },
        'arr_airport_id' => function () {
            return factory(\App\Models\Airport::class)->create()->id;
        },
        'alt_airport_id' => function () {
            return factory(\App\Models\Airport::class)->create()->id;
        },
        'distance'             => $faker->numberBetween(1, 1000),
        'route'                => null,
        'level'                => 0,
        'dpt_time'             => $faker->time(),
        'arr_time'             => $faker->time(),
        'flight_time'          => $faker->numberBetween(60, 360),
        'load_factor'          => $faker->randomElement([15, 20, 50, 90, 100]),
        'load_factor_variance' => $faker->randomElement([15, 20, 50, 90, 100]),
        'has_bid'              => false,
        'active'               => true,
        'visible'              => true,
        'days'                 => 0,
        'start_date'           => null,
        'end_date'             => null,
        'created_at'           => $faker->dateTimeBetween('-1 week', 'now'),
        'updated_at'           => static function (array $flight) {
            return $flight['created_at'];
        },
    ];
});
