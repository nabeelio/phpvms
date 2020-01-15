<?php

namespace App\Repositories;

use App\Contracts\Repository;
use App\Models\Airport;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

class AirportRepository extends Repository implements CacheableInterface
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
     * Returns an airport, or a empty model with just some default values
     *
     * @param       $id
     * @param array $columns
     *
     * @return \App\Models\Airport|mixed|null
     */
    public function findWithoutFail($id, array $columns = ['*'])
    {
        $value = parent::findWithoutFail($id, $columns);
        if (!empty($value)) {
            return $value;
        }

        // Not found, return a 'generic' airport object
        return new Airport([
            'id'   => $id,
            'icao' => $id,
            'iata' => $id,
            'name' => $id,
            'lat'  => 0,
            'lon'  => 0,
        ]);
    }

    /**
     * Return the list of airports formatted for a select box
     *
     * @param mixed $add_blank
     * @param mixed $only_hubs
     *
     * @return array
     */
    public function selectBoxList($add_blank = false, $only_hubs = false): array
    {
        $retval = [];
        $where = [];

        if ($only_hubs) {
            $where['hub'] = 1;
        }

        $items = $this->orderBy('icao', 'asc')->findWhere($where);

        if ($add_blank) {
            $retval[''] = '';
        }

        foreach ($items as $i) {
            $s = $i->icao.' - '.$i->name;
            if (!$only_hubs && $i->hub) {
                $s .= ' (hub)';
            }

            $retval[$i->icao] = $s;
        }

        return $retval;
    }
}
