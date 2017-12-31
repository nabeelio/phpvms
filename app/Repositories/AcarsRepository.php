<?php

namespace App\Repositories;

use App\Models\Acars;
use App\Models\Pirep;
use App\Models\Enums\PirepState;
use App\Models\Enums\PirepStatus;

use App\Repositories\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;

class AcarsRepository extends BaseRepository //implements CacheableInterface
{
    //use CacheableRepository;

    public function model()
    {
        return Acars::class;
    }

    public function forPirep($pirep_id)
    {
        return $this->findWhere(['pirep_id' => $pirep_id]);
    }

    /**
     * Get all of the PIREPS that are in-progress, and then
     * get the latest update for those flights
     * @return Pirep
     */
    public function getPositions()
    {
        return Pirep::with(['airline', 'position'])
                ->where(['state' => PirepState::IN_PROGRESS])
                ->get();
    }

    /**
     * @return $this
     */
    public function getAllAcarsPoints()
    {
        return Pirep::with('acars')->where([
            'state' => PirepState::IN_PROGRESS
        ]);
    }
}
