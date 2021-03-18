<?php

use App\Models\Airline;
use App\Models\Enums\UserState;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;

$factory->define(App\Models\Role::class, function (Faker $faker) {
    return [
        'id'                               => null,
        'name'                             => $faker->name,
        'display_name'                     => $faker->name,
        'read_only'                        => false,
        'disable_activity_checks'          => $faker->boolean(),
    ];
});
