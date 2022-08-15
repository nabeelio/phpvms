<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

namespace App\Database\Factories;

use App\Contracts\Factory;
use App\Models\Journal;
use App\Models\JournalTransaction;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class JournalTransactionsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JournalTransaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_group' => Uuid::uuid4()->toString(),
            'journal_id'        => fn () => Journal::factory()->create()->id,
            'credit'            => $this->faker->numberBetween(100, 10000),
            'debit'             => $this->faker->numberBetween(100, 10000),
            'currency'          => 'USD',
            'memo'              => $this->faker->sentence(6),
            'post_date'         => Carbon::now('UTC')->toDateTimeString(),
        ];
    }
}
