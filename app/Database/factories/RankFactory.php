<?php

use Faker\Generator as Faker;

/*
 * id: 2
    name: Junior First Officer
    hours: 10
    auto_approve_acars: 1
    auto_approve_manual: 1
 */
$factory->define(App\Models\Rank::class, function (Faker $faker) {
    return [
        'id' => $faker->unique()->numberBetween(10, 10000),
        'name' => $faker->unique()->text(50),
        'hours' => $faker->numberBetween(10, 50),
        'auto_approve_acars' => 0,
        'auto_approve_manual' => 0,
        'auto_promote' => 0,
    ];
});
