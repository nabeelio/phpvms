<?php

namespace App\Repositories;

use App\Models\Airport;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;


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
    public function selectBoxList()
    {
        $retval = [];
        $items = $this->all();
        foreach ($items as $i) {
            $retval[$i->icao] = $i->icao . ' - ' . $i->name;
        }

        return $retval;
    }
}
