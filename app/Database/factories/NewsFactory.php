<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

namespace App\Database\Factories;

use App\Contracts\Factory;
use App\Models\News;

class NewsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = News::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id'      => null,
            'user_id' => fn () => \App\Models\User::factory()->create()->id,
            'subject' => $this->faker->text(),
            'body'    => $this->faker->sentence,
        ];
    }
}
