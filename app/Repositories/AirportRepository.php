<?php

namespace App\Repositories;

use App\Models\Airport;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;


class AirportRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    protected $fieldSearchable = [
        'icao' => 'like',
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
    public function selectBoxList($add_blank=false, $only_hubs=false): array
    {
        $retval = [];
        $where = [];

        if($only_hubs) {
            $where['hub'] = 1;
        }

        $items = $this->orderBy('icao', 'asc')->findWhere($where);

        if ($add_blank) {
            $retval[''] = '';
        }

        foreach ($items as $i) {
            $retval[$i->icao] = $i->icao . ' - ' . $i->name;
        }

        return $retval;
    }
}
