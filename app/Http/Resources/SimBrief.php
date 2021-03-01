<?php

namespace App\Http\Resources;

use App\Contracts\Resource;

/**
 * @mixin \App\Models\SimBrief
 */
class SimBrief extends Resource
{
    public function toArray($request)
    {
        $data = [
            'id'       => $this->id,
            'url'      => url(route('api.flights.briefing', ['id' => $this->id])),
        ];

        try {
            if (!empty($this->fare_data)) {
                $fares = [];
                $fare_data = json_decode($this->fare_data, true);
                foreach ($fare_data as $fare) {
                    $fares[] = new \App\Models\Fare($fare);
                }

                $this->aircraft->subfleet->fares = collect($fares);
            }
        } catch (\Exception $e) {
            // Invalid fare data
        }

        $data['subfleet'] = new Subfleet($this->aircraft->subfleet);

        return $data;
    }
}
