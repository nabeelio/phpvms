<?php

namespace App\Services;

use App\Models\User;
use App\Models\Rank;
use Illuminate\Support\Facades\Cache;



class PilotService extends BaseService
{

    public function adjustFlightCount(User &$pilot, int $count): User
    {
        $pilot->refresh();
        $pilot->flights = $pilot->flights + $count;
        $pilot->save();

        return $pilot;
    }

    public function adjustFlightHours(User &$pilot, int $hours): User
    {
        $pilot->refresh();
        $pilot->flight_time = $pilot->flight_time + $hours;
        $pilot->save();

        return $pilot;
    }

    public function calculatePilotRank(User &$pilot): User
    {
        $pilot->refresh();
        $pilot_hours = $pilot->flight_time / 3600;

        # TODO: Cache
        $ranks = Cache::remember(
            config('phpvms.cache_keys.RANKS_PILOT_LIST')['key'],
            config('phpvms.cache_keys.RANKS_PILOT_LIST')['time'],
            function () {
                return Rank::where('auto_promote', true)->orderBy('hours', 'asc')->get();
            });

        foreach ($ranks as $rank) {
            if($rank->hours > $pilot_hours) {
                break;
            } else {
                $pilot->rank_id = $rank->id;
            }
        }

        $pilot->save();

        return $pilot;
    }

}
