<?php

namespace App\Cron\Nightly;

use App\Events\CronNightly;
use App\Interfaces\Listener;
use App\Models\Enums\ExpenseType;
use App\Services\Finance\RecurringFinanceService;

/**
 * Go through and apply any finances that are daily
 */
class ApplyExpenses extends Listener
{
    private $financeSvc;

    /**
     * ApplyExpenses constructor.
     *
     * @param RecurringFinanceService $financeSvc
     */
    public function __construct(RecurringFinanceService $financeSvc)
    {
        $this->financeSvc = $financeSvc;
    }

    /**
     * Apply all of the expenses for a day
     *
     * @param CronNightly $event
     *
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function handle(CronNightly $event): void
    {
        $this->financeSvc->processExpenses(ExpenseType::DAILY);
    }
}
