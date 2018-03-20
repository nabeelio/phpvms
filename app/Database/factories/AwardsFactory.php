<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Award::class, function (Faker $faker) {
    return [
        'id'               => null,
        'name'             => $faker->name,
        'description'      => $faker->text(10),
        'ref_class'        => null,
        'ref_class_params' => null,
    ];
});
