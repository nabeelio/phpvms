<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Role::class, function (Faker $faker) {
    return [
        'id'                      => null,
        'name'                    => $faker->name,
        'display_name'            => $faker->name,
        'read_only'               => false,
        'disable_activity_checks' => $faker->boolean(),
    ];
});
