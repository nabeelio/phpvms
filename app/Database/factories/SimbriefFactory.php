<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(App\Models\SimBrief::class, function (Faker $faker) {
    return [
        'id'         => $faker->unique()->numberBetween(10, 10000000),
        'user_id'    => null,
        'flight_id'  => null,
        'pirep_id'   => null,
        'acars_xml'  => '',
        'ofp_xml'    => '',
        'created_at' => Carbon::now('UTC')->toDateTimeString(),
        'updated_at' => function (array $sb) {
            return $sb['created_at'];
        },
    ];
});
