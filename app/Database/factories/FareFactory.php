<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Fare::class, function (Faker $faker) {
    return [
        'id' => null,
        'code' => $faker->text(5),
        'name' => $faker->text(20),
        'price' => $faker->randomFloat(2, 100, 1000),
        'cost' => function (array $fare) {
            return round($fare['price'] / 2);
        },
        'capacity' => $faker->randomFloat(0, 20, 500),
    ];
});
