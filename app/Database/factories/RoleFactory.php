<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

namespace App\Database\Factories;

use App\Contracts\Factory;
use App\Models\Role;

class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id'                      => null,
            'name'                    => $this->faker->name,
            'display_name'            => $this->faker->name,
            'read_only'               => false,
            'disable_activity_checks' => $this->faker->boolean(),
        ];
    }
}
