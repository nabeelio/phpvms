<?php

namespace Modules\Importer\Services\Importers;

use Modules\Importer\Services\BaseImporter;

class ExpenseImporter extends BaseImporter
{
    protected $table = 'expenses';

    /**
     * {@inheritdoc}
     */
    public function run($start = 0)
    {
        $this->comment('--- FLIGHT SCHEDULE IMPORT ---');

        $count = 0;
        $rows = $this->db->readRows($this->table, $this->idField, $start);
        foreach ($rows as $row) {
            //
        }
    }
}
