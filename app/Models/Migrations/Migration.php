<?php
/**
 * Migration base class with some extra functionality
 */

namespace App\Models\Migrations;

use DB;
use Illuminate\Database\Migrations\Migration as MigrationBase;

class Migration extends MigrationBase
{
    /**
     * Add rows to a table
     * @param $table
     * @param $rows
     */
    public function addData($table, $rows)
    {
        foreach ($rows as $row) {
            try {
                DB::table($table)->insert($row);
            } catch (Exception $e) {
                # setting already exists, just ignore it
                if ($e->getCode() === 23000) {
                    continue;
                }
            }
        }
    }
}
