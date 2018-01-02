<?php
/**
 * Migration base class with some extra functionality
 */

namespace App\Models\Migrations;

use DB;
use Illuminate\Database\Migrations\Migration as MigrationBase;

class Migration extends MigrationBase
{
    protected $counters;


    /**
     * Just make sure the dotted format converts to all underscores
     */
    public function formatSettingId($id)
    {
        return str_replace('.', '_', $id);
    }

    /**
     * Create a counter for groups with the start index. E.g:
     * pireps: 10
     * users: 30
     *
     * When calling getNextOrderNumber(users) 31, will be returned, then 32, and so on
     * @param array $groups
     */
    public function addCounterGroups(array $groups)
    {
        foreach($groups as $group => $start) {
            $this->counters[$group] = $start;
        }
    }

    /**
     * Get the next increment number from a group
     * @param $group
     * @return int
     */
    public function getNextOrderNumber($group)
    {
        if(!isset($this->counters[$group])) {
            return 0;
        }

        $idx = $this->counters[$group];
        ++$this->counters[$group];

        return $idx;
    }

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
