<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

namespace App\Database\Factories;

use App\Contracts\Factory;
use App\Models\Enums\NavaidType;
use App\Models\Navdata;

class NavdataFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Navdata::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id'   => str_replace(' ', '', str_replace('.', '', $this->faker->unique()->text(5))),
            'name' => str_replace('.', '', $this->faker->unique()->word),
            'type' => $this->faker->randomElement([NavaidType::VOR, NavaidType::NDB]),
            'lat'  => $this->faker->latitude,
            'lon'  => $this->faker->longitude,
            'freq' => $this->faker->randomFloat(2, 100, 1000),
        ];
    }
}
