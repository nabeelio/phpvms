<?php

namespace App\Repositories;

use App\Contracts\Repository;
use App\Models\Typerating;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

class TypeRatingRepository extends Repository implements CacheableInterface
{
    use CacheableRepository;

    protected $fieldSearchable = [
        'name' => 'like',
        'type' => 'like',
    ];

    public function model()
    {
        return Typerating::class;
    }

    public function selectBoxList($add_blank = false, $only_active = true): array
    {
        $retval = [];
        $where = [
            'active' => $only_active,
        ];

        $items = $this->findWhere($where);

        if ($add_blank) {
            $retval[''] = '';
        }

        foreach ($items as $i) {
            $retval[$i->id] = $i->name;
        }

        return $retval;
    }
}
