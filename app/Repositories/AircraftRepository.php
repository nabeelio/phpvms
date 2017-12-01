<?php

namespace App\Repositories;

use App\Models\Aircraft;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

class AircraftRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    protected $fieldSearchable = [
        'name' => 'like',
        'registration' => 'like',
        'active',
    ];

    public function model()
    {
        return Aircraft::class;
    }

    /**
     * Return the list of aircraft formatted for a select box
     * @return array
     */
    public function selectBoxList()
    {
        $retval = [];
        $items = $this->all();

        foreach ($items as $i) {
            $retval[$i->id] = $i->subfleet->name . ' - ' . $i->name . ' (' . $i->registration . ')';
        }

        return $retval;
    }
}
