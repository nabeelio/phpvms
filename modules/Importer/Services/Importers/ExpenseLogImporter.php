<?php

namespace Modules\Importer\Services\Importers;

use App\Models\Airline;
use App\Models\Expense;
use App\Services\FinanceService;
use App\Support\Money;
use Modules\Importer\Services\BaseImporter;

class ExpenseLogImporter extends BaseImporter
{
    protected $table = 'expenselog';

    /**
     * {@inheritdoc}
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function run($start = 0)
    {
        $this->comment('--- EXPENSE LOG IMPORT ---');

        /** @var FinanceService $financeSvc */
        $financeSvc = app(FinanceService::class);
        $airline = Airline::first();

        $count = 0;
        $rows = $this->db->readRows($this->table, $this->idField, $start);
        foreach ($rows as $row) {
            $expense_id = $this->idMapper->getMapping('expenses', $row->name);
            $expense = Expense::find($expense_id);

            $debit = Money::createFromAmount($expense->amount);

            $financeSvc->debitFromJournal(
                $airline->journal,
                $debit,
                $airline,
                'Expense: '.$expense->name,
                $expense->transaction_group ?? 'Expenses',
                'expense'
            );

            $count++;
        }

        $this->info('Imported '.$count.' expense logs');
    }
}
