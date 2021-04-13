<?php

use App\Models\Journal;
use Carbon\Carbon;
use Faker\Generator as Faker;
use Ramsey\Uuid\Uuid;

$factory->define(App\Models\JournalTransaction::class, function (Faker $faker) {
    return [
        'transaction_group' => Uuid::uuid4()->toString(),
        'journal_id'        => function () {
            return factory(Journal::class)->create()->id;
        },
        'credit'    => $faker->numberBetween(100, 10000),
        'debit'     => $faker->numberBetween(100, 10000),
        'currency'  => 'USD',
        'memo'      => $faker->sentence(6),
        'post_date' => Carbon::now('UTC'),
    ];
});
