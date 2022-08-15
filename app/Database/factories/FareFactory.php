<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

namespace App\Database\Factories;

use App\Contracts\Factory;
use App\Models\Fare;

class FareFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Fare::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id'       => null,
            'code'     => $this->faker->unique()->text(50),
            'name'     => $this->faker->text(50),
            'price'    => $this->faker->randomFloat(2, 100, 1000),
            'cost'     => fn (array $fare) => round($fare['price'] / 2),
            'capacity' => $this->faker->randomFloat(0, 20, 500),
        ];
    }
}
