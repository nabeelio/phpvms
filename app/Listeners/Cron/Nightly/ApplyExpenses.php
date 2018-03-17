<?php

namespace App\Listeners\Cron\Nightly;

use App\Events\CronNightly;
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
     * Apply all of the expenses for a day
     * @param CronNightly $event
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function handle(CronNightly $event): void
    {
        $this->dfSvc->processExpenses(ExpenseType::DAILY);
    }
}
