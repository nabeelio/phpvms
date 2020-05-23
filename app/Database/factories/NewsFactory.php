<?php

use Faker\Generator as Faker;

$factory->define(App\Models\News::class, function (Faker $faker) {
    return [
        'id'      => null,
        'user_id' => function () {
            return factory(\App\Models\User::class)->create()->id;
        },
        'subject' => $faker->text(),
        'body'    => $faker->sentence,
    ];
});
