<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Journal::class, function (Faker $faker) {
    return [
        'currency' => 'USD',
    ];
});
