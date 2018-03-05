<?php

use Faker\Generator as Faker;

$factory->define(App\Models\SubfleetExpense::class, function (Faker $faker) {
    return [
        'subfleet_id' => null,
        'name' => $faker->text(20),
        'amount' => $faker->randomFloat(2, 100, 1000),
    ];
});
