<?php
/**
 *
 */

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Yaml\Yaml;
use Webpatser\Uuid\Uuid;
use Log;

class Database
{
    /**
     * @return string
     */
    protected static function time(): string
    {
        return Carbon::now('UTC');
    }

    /**
     * @param      $yaml_file
     * @param bool $ignore_errors
     * @return array
     * @throws \Exception
     */
    public static function seed_from_yaml_file($yaml_file, $ignore_errors = false): array
    {
        $yml = file_get_contents($yaml_file);
        return static::seed_from_yaml($yml, $ignore_errors);
    }

    /**
     * @param      $yml
     * @param bool $ignore_errors
     * @return array
     * @throws \Exception
     */
    public static function seed_from_yaml($yml, $ignore_errors = false): array
    {
        $imported = [];
        $yml = Yaml::parse($yml);
        foreach ($yml as $table => $rows) {
            $imported[$table] = 0;

            foreach ($rows as $row) {
                try {
                    static::insert_row($table, $row);
                } catch (QueryException $e) {
                    if ($ignore_errors) {
                        continue;
                    }

                    throw $e;
                }

                ++$imported[$table];
            }
        }

        return $imported;
    }

    /**
     * @param      $table
     * @param      $row
     * @return mixed
     * @throws \Exception
     */
    public static function insert_row($table, $row)
    {
        # encrypt any password fields
        if (array_key_exists('password', $row)) {
            $row['password'] = bcrypt($row['password']);
        }

        # if any time fields are == to "now", then insert the right time
        foreach ($row as $column => $value) {
            if (strtolower($value) === 'now') {
                $row[$column] = static::time();
            }
        }

        try {
            DB::table($table)->insert($row);
        } catch (QueryException $e) {
            Log::error($e);
            throw $e;
        }

        return $row;
    }
}
