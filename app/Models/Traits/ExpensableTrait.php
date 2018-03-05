<?php

namespace App\Models\Traits;

use App\Models\Expense;

trait ExpensableTrait
{

    /**
     * Initialize a new journal when a new record is created
     */
    public static function bootExpensableTrait()
    {
        /*static::created(function ($model) {
            $model->initJournal(config('phpvms.currency'));
        });*/
    }

    /**
     * Morph to Expenses.
     * @return mixed
     */
    public function expenses()
    {
        return $this->morphToMany(Expense::class, 'expensable');
    }
}
