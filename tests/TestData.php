<?php

namespace Tests;

use App\Models\Aircraft;
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
        $user = factory(User::class)->create(array_merge([
            'flight_time' => 1000,
            'rank_id'     => $rank->id,
        ], $attrs));

        return $user;
    }

    /**
     * Create a new PIREP with a proper subfleet/rank/user and an
     * aircraft that the user is allowed to fly
     *
     * @return \App\Models\Pirep
     */
    protected function createPirep()
    {
        $subfleet = $this->createSubfleetWithAircraft(2);
        $rank = $this->createRank(10, [$subfleet['subfleet']->id]);
        $this->user = factory(\App\Models\User::class)->create([
            'rank_id' => $rank->id,
        ]);

        // Return a Pirep model
        $pirep = factory(\App\Models\Pirep::class)->make([
            'aircraft_id' => $subfleet['aircraft']->random()->id,
        ]);

        return $pirep;
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
}
