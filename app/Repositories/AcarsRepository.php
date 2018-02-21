<?php

namespace App\Repositories;

use App\Models\Acars;
use App\Models\Enums\AcarsType;
use App\Models\Enums\PirepState;
use App\Models\Pirep;

class AcarsRepository extends BaseRepository //implements CacheableInterface
{
    //use CacheableRepository;

    public function model()
    {
        return Acars::class;
    }

    /**
     * @param $pirep_id
     * @param $type
     * @return mixed
     */
    public function forPirep($pirep_id, $type)
    {
        $where = [
            'pirep_id' => $pirep_id,
            'type' => $type,
        ];

        $order_by = 'created_at';
        if($type === AcarsType::ROUTE) {
            $order_by = 'order';
        }

        return $this->orderBy('order', 'asc')->findWhere($where);
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
