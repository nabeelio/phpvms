<?php

namespace App\Repositories;

use App\Models\Expense;
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
}
