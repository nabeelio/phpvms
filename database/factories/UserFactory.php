<?php

use Faker\Generator as Faker;

$factory->define(App\Models\User::class, function (Faker $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'api_key' => $faker->sha1,
        'flights' => $faker->numberBetween(0, 1000),
        'flight_time' => $faker->numberBetween(0, 10000),
        'remember_token' => str_random(10),
    ];
});
