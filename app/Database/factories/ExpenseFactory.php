<?php

use App\Models\Enums\ExpenseType;
use Faker\Generator as Faker;

$factory->define(App\Models\Expense::class, function (Faker $faker) {
    return [
        'id'           => null,
        'airline_id'   => null,
        'name'         => $faker->text(20),
        'amount'       => $faker->randomFloat(2, 100, 1000),
        'type'         => ExpenseType::FLIGHT,
        'multiplier'   => false,
        'ref_model'    => \App\Models\Expense::class,
        'ref_model_id' => null,
        'active'       => true,
    ];
});
