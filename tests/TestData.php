<?php
/**
 *
 */

namespace Tests;

class TestData
{
    /**
     * Create a rank and associate the given subfleet IDs with it
     * @param int $hours
     * @param array $subfleet_ids
     * @return mixed
     */
    public static function createRank($hours=0, $subfleet_ids=[])
    {
        $attrs = [];

        if($hours === null) {
            $attrs['hours'] = $hours;
        }

        $rank = factory(\App\Models\Rank::class)->create($attrs);
        if(!empty($subfleet_ids)) {
            $rank->subfleets()->syncWithoutDetaching($subfleet_ids);
        }

        return $rank;
    }

    /**
     * Create a subfleet with a number of aircraft assigned
     * @param null $aircraft_count
     * @return mixed
     */
    public static function createSubfleetWithAircraft($aircraft_count = null, $airport_id=null)
    {
        $subfleet = factory(\App\Models\Subfleet::class)->create();

        if($aircraft_count === null) {
            $aircraft_count = \random_int(2, 10);
        }

        $aircraft = factory(\App\Models\Aircraft::class, $aircraft_count)->create([
            'subfleet_id' => $subfleet->id,
            'airport_id' => $airport_id,
        ]);

        return [
            'subfleet' => $subfleet,
            'aircraft' => $aircraft,
        ];
    }
}
