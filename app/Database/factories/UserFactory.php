<?php

use Faker\Generator as Faker;
use App\Models\Enums\UserState;

$factory->define(App\Models\User::class, function (Faker $faker)
{
    static $password;

    return [
        #'id' => $faker->unique()->numberBetween(10, 10000),
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => $password ?: $password = Hash::make('secret'),
        'api_key' => $faker->sha1,
        'airline_id' => function () {
            return factory(App\Models\Airline::class)->create()->id;
        },
        'rank_id' => 1,
        'flights' => $faker->numberBetween(0, 1000),
        'flight_time' => $faker->numberBetween(0, 10000),
        'transfer_time' => $faker->numberBetween(0, 10000),
        'state' => UserState::ACTIVE,
        'remember_token' => $faker->unique()->text(5),
    ];
});
