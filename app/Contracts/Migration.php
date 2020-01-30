<?php

namespace App\Contracts;

use App\Support\Database;
use DB;
use Illuminate\Support\Facades\Log;

/**
 * Class Migration
 */
abstract class Migration extends \Illuminate\Database\Migrations\Migration
{
    /**
     * At a minimum, this function needs to be implemented
     *
     * @return mixed
     */
    abstract public function up();

    /**
     * A method to reverse a migration doesn't need to be made
     */
    public function down()
    {
    }

    /**
     * Seed a YAML file into the database
     *
     * @param string $file Full path to yml file to seed
     */
    public function seedFile($file): void
    {
        try {
            $path = base_path($file);
            Database::seed_from_yaml_file($path, false);
        } catch (\Exception $e) {
            Log::error('Unable to load '.$file.' file');
            Log::error($e);
        }
    }
    /**
     * Add rows to a table
     *
     * @param $table
     * @param $rows
     */
    public function addData($table, $rows)
    {
        foreach ($rows as $row) {
            try {
                DB::table($table)->insert($row);
            } catch (\Exception $e) {
                // setting already exists, just ignore it
                if ($e->getCode() === 23000) {
                    continue;
                }
            }
        }
    }
}
