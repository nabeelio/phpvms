<?php

namespace App\Listeners\Cron\Monthly;

use App\Events\CronMonthly;
use App\Models\Enums\ExpenseType;
use App\Services\Finance\RecurringFinanceService;

/**
 * Go through and apply any finances that are daily
 * @package App\Listeners\Cron\Nightly
 */
class ApplyExpenses
{
    private $dfSvc;

    public function __construct(RecurringFinanceService $dfSvc)
    {
        $this->dfSvc = $dfSvc;
    }

    /**
     * Apply all of the expenses for a month
     * @param CronMonthly $event
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function handle(CronMonthly $event)
    {
        $this->dfSvc->processExpenses(ExpenseType::MONTHLY);
    }
}
