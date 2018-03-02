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
     * @return Collection
     */
    public function getAllForType($type, $airline_id=null)
    {
        $expenses = $this->findWhere([
            'type' => $type,
            ['airline_id', '=', null]
        ]);

        if($airline_id) {
            $airline_expenses = $this->findWhere([
                'type' => $type,
                'airline_id' => $airline_id
            ]);

            $expenses = $expenses->concat($airline_expenses);
        }

        return $expenses;
    }
}
