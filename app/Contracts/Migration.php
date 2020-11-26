<?php

namespace App\Contracts;

use App\Support\Database;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
     * Add a module and enable it
     *
     * @param array $attrs
     */
    public function addModule(array $attrs)
    {
        $module = array_merge([
            'enabled'    => true,
            'created_at' => DB::raw('NOW()'),
            'updated_at' => DB::raw('NOW()'),
        ], $attrs);

        try {
            DB::table('modules')->insert($module);
        } catch (Exception $e) {
            // setting already exists, just ignore it
            if ($e->getCode() === 23000) {
                return;
            }
        }
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
        } catch (Exception $e) {
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
            } catch (Exception $e) {
                // setting already exists, just ignore it
                if ($e->getCode() === 23000) {
                    continue;
                }
            }
        }
    }

    /**
     * Add an award from the migrations (for example, if you're adding an award module)
     *
     * @param array $award See \App\Models\Awardv
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function addAward(array $award)
    {
        $validator = Validator::make($award, \App\Models\Award::$rules);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $awardModel = new \App\Models\Award($award);
        $awardModel->save();
    }
}
