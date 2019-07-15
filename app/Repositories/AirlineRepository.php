<?php

namespace App\Repositories;

use App\Contracts\Repository;
use App\Models\Airline;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

/**
 * Class AirlineRepository
 */
class AirlineRepository extends Repository implements CacheableInterface
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
     *
     * @param mixed $add_blank
     *
     * @return array
     */
    public function selectBoxList($add_blank = false): array
    {
        $retval = [];
        $items = $this->all();

        if ($add_blank) {
            $retval[''] = '';
        }

        foreach ($items as $i) {
            $retval[$i->id] = $i->name;
        }

        return $retval;
    }
}
