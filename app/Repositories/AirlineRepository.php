<?php

namespace App\Repositories;

use App\Models\Airline;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;


class AirlineRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    protected $fieldSearchable = [
        'code',
        'name' => 'like',
    ];

    public function model()
    {
        return Airline::class;
    }

    /**
     * Return the list of airline formatted for a select box
     * @return array
     */
    public function selectBoxList()
    {
        $retval = [];
        $items = $this->all();

        foreach ($items as $i) {
            $retval[$i->id] = $i->name;
        }

        return $retval;
    }
}
