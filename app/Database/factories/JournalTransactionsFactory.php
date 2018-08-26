<?php

use Faker\Generator as Faker;

$factory->define(App\Models\JournalTransactions::class, function (Faker $faker) {
    return [
        'transaction_group' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
        'journal_id'        => function () {
            return factory(App\Models\Journal::class)->create()->id;
        },
        'credit'    => $faker->numberBetween(100, 10000),
        'debit'     => $faker->numberBetween(100, 10000),
        'currency'  => 'USD',
        'memo'      => $faker->sentence(6),
        'post_date' => \Carbon\Carbon::now(),
    ];
});
