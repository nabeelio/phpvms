<?php

namespace App\Repositories;

use App\Models\Airline;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;


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
    public function selectBoxList($add_blank=false): array
    {
        $retval = [];
        $items = $this->all();

        if($add_blank) {
            $retval[''] = '';
        }

        foreach ($items as $i) {
            $retval[$i->id] = $i->name;
        }

        return $retval;
    }
}
