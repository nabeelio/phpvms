<?php

namespace App\Cron\Monthly;

use App\Events\CronMonthly;
use App\Interfaces\Listener;
use App\Models\Enums\ExpenseType;
use App\Services\Finance\RecurringFinanceService;

/**
 * Go through and apply any finances that are daily
 * @package App\Listeners\Cron\Nightly
 */
class ApplyExpenses extends Listener
{
    private $financeSvc;

    /**
     * ApplyExpenses constructor.
     * @param RecurringFinanceService $financeSvc
     */
    public function __construct(RecurringFinanceService $financeSvc)
    {
        $this->financeSvc = $financeSvc;
    }

    /**
     * Apply all of the expenses for a month
     * @param CronMonthly $event
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function handle(CronMonthly $event): void
    {
        $this->financeSvc->processExpenses(ExpenseType::MONTHLY);
    }
}
