<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Airport::class, function (Faker $faker) {
    return [
        'code' => 'Y',
        'name' => 'Economy',
        'price' => '100',
        'capacity' => '200',
    ];
});
