<?php

namespace Tests;

use App\Models\Aircraft;
use App\Models\Enums\UserState;
use App\Models\Flight;
use App\Models\Pirep;
use App\Models\Role;
use App\Models\Subfleet;
use App\Models\User;
use Exception;

trait TestData
{
    /**
     * @param array $attrs Additional user attributes
     *
     * @throws Exception
     *
     * @return User
     */
    public function createUser(array $attrs = []): User
    {
        $subfleet = $this->createSubfleetWithAircraft(1);
        $rank = $this->createRank(2, [$subfleet['subfleet']->id]);

        return factory(User::class)->create(array_merge([
            'flight_time' => 1000,
            'rank_id'     => $rank->id,
            'state'       => UserState::ACTIVE,
        ], $attrs));
    }

    /**
     * Create a new PIREP with a proper subfleet/rank/user and an
     * aircraft that the user is allowed to fly
     *
     * @param array $user_attrs  Additional attributes for the user
     * @param array $pirep_attrs Additional attributes for the PIREP
     *
     * @throws \Exception
     *
     * @return \App\Models\Pirep
     */
    protected function createPirep(array $user_attrs = [], array $pirep_attrs = [])
    {
        $subfleet = $this->createSubfleetWithAircraft(2);
        $rank = $this->createRank(10, [$subfleet['subfleet']->id]);

        /** @var User user */
        $this->user = factory(User::class)->create(array_merge([
            'rank_id' => $rank->id,
        ], $user_attrs));

        // Return a Pirep model
        return factory(Pirep::class)->make(array_merge([
            'user_id'     => $this->user->id,
            'aircraft_id' => $subfleet['aircraft']->random()->id,
        ], $pirep_attrs));
    }

    /**
     * Create a rank and associate the given subfleet IDs with it
     *
     * @param int   $hours
     * @param array $subfleet_ids
     *
     * @return mixed
     */
    public function createRank(int $hours, array $subfleet_ids)
    {
        $attrs = [];

        if ($hours === null) {
            $attrs['hours'] = $hours;
        }

        $rank = factory(\App\Models\Rank::class)->create($attrs);
        if (!empty($subfleet_ids)) {
            $rank->subfleets()->syncWithoutDetaching($subfleet_ids);
        }

        return $rank;
    }

    /**
     * Add a single flight
     *
     * @param       $user
     * @param array $flight_properties
     *
     * @return mixed
     */
    public function addFlight($user, $flight_properties = [])
    {
        $opts = array_merge([
            'airline_id' => $user->airline_id,
        ], $flight_properties);

        $flight = factory(Flight::class)->create($opts);

        $flight->subfleets()->syncWithoutDetaching([
            factory(Subfleet::class)->create([
                'airline_id' => $user->airline_id,
            ])->id,
        ]);

        return $flight;
    }

    /**
     * Add a given number of flights for a subfleet
     *
     * @param $subfleet
     * @param $num_flights
     *
     * @return \App\Models\Flight[]
     */
    public function addFlightsForSubfleet($subfleet, $num_flights)
    {
        return factory(Flight::class, $num_flights)->create([
            'airline_id' => $subfleet->airline->id,
        ])->each(function (Flight $f) use ($subfleet) {
            $f->subfleets()->syncWithoutDetaching([$subfleet->id]);
            $f->refresh();
        });
    }

    /**
     * Create a subfleet with a number of aircraft assigned
     *
     * @param null $aircraft_count
     * @param null $airport_id
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function createSubfleetWithAircraft($aircraft_count = null, $airport_id = null)
    {
        /** @var Subfleet $subfleet */
        $subfleet = factory(Subfleet::class)->create([
            'ground_handling_multiplier' => '100',
        ]);

        if ($aircraft_count === null) {
            $aircraft_count = \random_int(2, 10);
        }

        $aircraft = factory(Aircraft::class, $aircraft_count)->create([
            'subfleet_id' => $subfleet->id,
            'airport_id'  => $airport_id,
        ]);

        return [
            'subfleet' => $subfleet,
            'aircraft' => $aircraft,
        ];
    }

    /**
     * Create a role
     *
     * @param array $attrs Additional role attributes
     *
     * @return Role
     */
    public function createRole(array $attrs = []): Role
    {
        return factory(Role::class)->create($attrs);
    }
}
