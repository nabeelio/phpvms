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
        'id'                   => null,
        'name'                 => $faker->unique()->text(50),
        'hours'                => $faker->numberBetween(10, 50),
        'acars_base_pay_rate'  => $faker->numberBetween(10, 100),
        'manual_base_pay_rate' => $faker->numberBetween(10, 100),
        'auto_approve_acars'   => 0,
        'auto_approve_manual'  => 0,
        'auto_promote'         => 0,
    ];
});
