<?php
/**
 * Migration base class with some extra functionality
 */

namespace App\Models\Migrations;

use DB;
use App\Models\Setting;
use Illuminate\Database\Migrations\Migration as MigrationBase;

class Migration extends MigrationBase
{
    private $counters = [];
    private $offsets = [];

    /**
     * Dynamically figure out the offset and the start number for a group.
     * This way we don't need to mess with how to order things
     * When calling getNextOrderNumber(users) 31, will be returned, then 32, and so on
     * @param $name
     * @param null $offset
     * @param int $start_offset
     */
    protected function addCounterGroup($name, $offset=null, $start_offset=0)
    {
        if($offset === null) {
            $group = DB::table('settings')
                        ->where('group', $name)
                        ->first();

            if($group === null) {
                $offset = (int) DB::table('settings')->max('offset');
                if($offset === null) {
                    $offset = 0;
                    $start_offset = 1;
                } else {
                    $offset += 100;
                    $start_offset = $offset + 1;
                }
            } else {

                # Now find the number to start from
                $start_offset = (int) DB::table('settings')->where('group', $name)->max('order');
                if($start_offset === null) {
                    $start_offset = $offset + 1;
                } else {
                    ++$start_offset;
                }

                $offset = $group->offset;
            }
        }

        $this->counters[$name] = $start_offset;
        $this->offsets[$name] = $offset;
    }

    /**
     * Get the next increment number from a group
     * @param $group
     * @return int
     */
    public function getNextOrderNumber($group): int
    {
        if(!\in_array($group, $this->counters, true)) {
            $this->addCounterGroup($group);
        }

        $idx = $this->counters[$group];
        ++$this->counters[$group];

        return $idx;
    }

    /**
     * @param $key
     * @param $attrs
     */
    public function addSetting($key, $attrs)
    {
        $group = $attrs['group'];
        $order = $this->getNextOrderNumber($group);

        $attrs = [
            'id' => Setting::formatKey($key),
            'key' => $key,
            'offset' => $this->offsets[$group],
            'order' => $order,
            'name' => $attrs['name'],
            'group' => $group,
            'value' => $attrs['value'],
            'default' => $attrs['value'],
            'type' => $attrs['type'],
            'description' => $attrs['description'],
        ];

        return $this->addData('settings', [$attrs]);
    }

    /**
     * Update a setting
     * @param $key
     * @param $value
     * @param array $attrs
     */
    public function updateSetting($key, $value, array $attrs=[])
    {
        $attrs['value'] = $value;
        DB::table('settings')
            ->where('id', $this->formatSettingId($key))
            ->update($attrs);
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
            } catch (\Exception $e) {
                # setting already exists, just ignore it
                if ($e->getCode() === 23000) {
                    continue;
                }
            }
        }
    }
}
