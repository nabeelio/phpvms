<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

namespace App\Database\Factories;

use App\Contracts\Factory;
use App\Models\Airline;
use App\Models\Enums\PirepSource;
use App\Models\Enums\PirepState;
use App\Models\Enums\PirepStatus;
use App\Models\Flight;
use App\Models\Pirep;
use Carbon\Carbon;

class PirepFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pirep::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /** @var Airline $airline */
        $airline = Airline::factory()->create();

        /** @var Flight $flight */
        $flight = Flight::factory()->create(['airline_id' => $airline->id]);

        return [
            'id'                  => $this->faker->unique()->numberBetween(10, 10000000),
            'airline_id'          => fn () => $airline->id,
            'user_id'             => fn () => \App\Models\User::factory()->create()->id,
            'aircraft_id'         => fn () => \App\Models\Aircraft::factory()->create()->id,
            'flight_id'           => fn () => $flight->id,
            'flight_number'       => fn () => $flight->flight_number,
            'route_code'          => null,
            'route_leg'           => null,
            'dpt_airport_id'      => fn () => $flight->dpt_airport_id,
            'arr_airport_id'      => fn () => $flight->arr_airport_id,
            'level'               => $this->faker->numberBetween(20, 400),
            'distance'            => $this->faker->randomFloat(2, 0, 6000),
            'planned_distance'    => $this->faker->randomFloat(2, 0, 6000),
            'flight_time'         => $this->faker->numberBetween(60, 360),
            'planned_flight_time' => $this->faker->numberBetween(60, 360),
            'zfw'                 => $this->faker->randomFloat(2),
            'block_fuel'          => $this->faker->randomFloat(2, 0, 1000),
            'fuel_used'           => fn (array $pirep) => round($pirep['block_fuel'] * .9, 2),
            'block_on_time'       => Carbon::now('UTC'),
            'block_off_time'      => fn (array $pirep) => $pirep['block_on_time']->subMinutes(
                $pirep['flight_time']
            ),
            'route'  => $this->faker->text(200),
            'notes'  => $this->faker->text(200),
            'source' => $this->faker->randomElement(
                [PirepSource::MANUAL, PirepSource::ACARS]
            ),
            'source_name'  => 'TestFactory',
            'state'        => PirepState::PENDING,
            'status'       => PirepStatus::SCHEDULED,
            'submitted_at' => Carbon::now('UTC')->toDateTimeString(),
            'created_at'   => Carbon::now('UTC')->toDateTimeString(),
            'updated_at'   => fn (array $pirep) => $pirep['created_at'],
        ];
    }
}
