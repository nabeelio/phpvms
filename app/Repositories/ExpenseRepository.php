<?php

namespace App\Repositories;

use App\Models\Expense;
use Illuminate\Support\Collection;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

/**
 * Class ExpenseRepository
 * @package App\Repositories
 */
class ExpenseRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    public function model()
    {
        return Expense::class;
    }

    /**
     * Get all of the expenses for a given type, and also
     * include expenses for a given airline ID
     * @param $type
     * @param null $airline_id
     * @param null $ref_class
     * @return Collection
     */
    public function getAllForType($type, $airline_id=null, $ref_class=null)
    {
        $where = [
            'type' => $type,
            ['airline_id', '=', null]
        ];

        if($ref_class) {
            $where['ref_class'] = $ref_class;
        }

        $expenses = $this->findWhere($where);

        if($airline_id) {

            $where = [
                'type' => $type,
                'airline_id' => $airline_id
            ];

            if ($ref_class) {
                $where['ref_class'] = $ref_class;
            }

            $airline_expenses = $this->findWhere($where);
            $expenses = $expenses->concat($airline_expenses);
        }

        return $expenses;
    }
}
