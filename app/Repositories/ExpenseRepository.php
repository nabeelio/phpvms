<?php

namespace App\Repositories;

use App\Interfaces\Repository;
use App\Models\Expense;
use Illuminate\Support\Collection;
use Prettus\Repository\Contracts\CacheableInterface;
use Prettus\Repository\Traits\CacheableRepository;

/**
 * Class ExpenseRepository
 * @package App\Repositories
 */
class ExpenseRepository extends Repository implements CacheableInterface
{
    use CacheableRepository;

    public function model()
    {
        return Expense::class;
    }

    /**
     * Get all of the expenses for a given type, and also
     * include expenses for a given airline ID
     * @param      $type
     * @param null $airline_id
     * @param null $ref_model
     * @return Collection
     */
    public function getAllForType($type, $airline_id = null, $ref_model = null)
    {
        $where = [
            'type' => $type,
            ['airline_id', '=', null]
        ];

        if ($ref_model) {
            if (\is_object($ref_model)) {
                $ref_model_type = \get_class($ref_model);
            } else {
                $ref_model_type = $ref_model;
            }

            if ($ref_model) {
                $where['ref_model'] = $ref_model_type;
            }
        }

        $expenses = $this->findWhere($where);

        if ($airline_id) {
            $where = [
                'type'       => $type,
                'airline_id' => $airline_id
            ];

            if ($ref_model) {
                $where['ref_model'] = $ref_model_type;
            }

            $airline_expenses = $this->findWhere($where);
            $expenses = $expenses->concat($airline_expenses);
        }

        return $expenses;
    }
}
