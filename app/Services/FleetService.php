<?php

namespace App\Services;

use App\Interfaces\Service;
use App\Models\Rank;
use App\Models\Subfleet;

/**
 * Class FleetService
 * @package App\Services
 */
class FleetService extends Service
{
    /**
     * @param Subfleet $subfleet
     * @param Rank     $rank
     * @param array    $overrides
     * @return Subfleet
     */
    public function addSubfleetToRank(Subfleet $subfleet, Rank $rank, array $overrides = [])
    {
        $subfleet->ranks()->syncWithoutDetaching([$rank->id]);

        if ($overrides) {
            $subfleet->ranks()->updateExistingPivot($rank->id, $overrides);
        }

        $subfleet->save();
        $subfleet->refresh();

        return $subfleet;
    }

    public function removeSubfleetFromRank(Subfleet $subfleet, Rank $rank)
    {
        $subfleet->ranks()->detach($rank->id);

        $subfleet->save();
        $subfleet->refresh();

        return $subfleet;
    }
}
