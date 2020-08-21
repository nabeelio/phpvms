<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Subfleet::class, function (Faker $faker) {
    return [
        'id'         => null,
        'airline_id' => function () {
            return factory(\App\Models\Airline::class)->create()->id;
        },
        'name'                       => $faker->unique()->text(50),
        'type'                       => $faker->unique()->text(7),
        'ground_handling_multiplier' => $faker->numberBetween(50, 200),
    ];
});
