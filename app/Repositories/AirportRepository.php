<?php

namespace App\Repositories;

use App\Models\Airport;
use App\Repositories\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;


class AirportRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    protected $fieldSearchable = [
        'icao',
        'name' => 'like',
    ];

    public function model()
    {
        return Airport::class;
    }

    /**
     * Return the list of airports formatted for a select box
     * @return array
     */
    public function selectBoxList($add_blank=false): array
    {
        $retval = [];
        $items = $this->orderBy('icao', 'asc')->all();

        if ($add_blank) {
            $retval[''] = '';
        }

        foreach ($items as $i) {
            $retval[$i->icao] = $i->icao . ' - ' . $i->name;
        }

        return $retval;
    }
}
