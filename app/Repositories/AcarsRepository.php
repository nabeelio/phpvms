<?php

namespace App\Repositories;

use App\Interfaces\Repository;
use App\Models\Acars;
use App\Models\Enums\AcarsType;
use App\Models\Enums\PirepState;
use App\Models\Pirep;
use Carbon\Carbon;

/**
 * Class AcarsRepository
 * @package App\Repositories
 */
class AcarsRepository extends Repository
{
    /**
     * @return string
     */
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
            'type'     => $type,
        ];

        switch ($type) {
            default:
            case AcarsType::FLIGHT_PATH:
            case AcarsType::LOG:
                $order_by = 'created_at';
                break;
            case AcarsType::ROUTE:
                $order_by = 'order';
                break;
        }

        return $this->orderBy($order_by, 'asc')->findWhere($where);
    }

    /**
     * Get all of the PIREPS that are in-progress, and then
     * get the latest update for those flights
     * @param int $live_time Age in hours of the oldest flights to show
     * @return Pirep
     */
    public function getPositions($live_time = 0)
    {
        $q = Pirep::with(['airline', 'position', 'aircraft'])
            ->where(['state' => PirepState::IN_PROGRESS]);

        if($live_time !== null && $live_time > 0) {
            $st = Carbon::now('UTC')->subHours($live_time);
            $q = $q->whereDate('created_at', '>=', $st);
        }

        $q = $q->orderBy('created_at', 'desc');
        return $q->get();
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
