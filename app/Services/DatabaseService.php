<?php

namespace App\Services;

use App\Interfaces\Service;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Yaml\Yaml;
use Webpatser\Uuid\Uuid;

/**
 * Class DatabaseService
 * @package App\Services
 */
class DatabaseService extends Service
{
    protected $time_fields = [
        'created_at',
        'updated_at'
    ];

    protected $uuid_tables = [
        'acars',
        'flights',
        'pireps',
    ];

    /**
     * @return string
     */
    protected function time(): string
    {
        return Carbon::now('UTC')->format('Y-m-d H:i:s');
    }

    /**
     * @param      $yaml_file
     * @param bool $ignore_errors
     * @return array
     * @throws \Exception
     */
    public function seed_from_yaml_file($yaml_file, $ignore_errors = false): array
    {
        $yml = file_get_contents($yaml_file);

        return $this->seed_from_yaml($yml, $ignore_errors);
    }

    /**
     * @param      $yml
     * @param bool $ignore_errors
     * @return array
     * @throws \Exception
     */
    public function seed_from_yaml($yml, $ignore_errors = false): array
    {
        $imported = [];
        $yml = Yaml::parse($yml);
        foreach ($yml as $table => $rows) {
            $imported[$table] = 0;

            foreach ($rows as $row) {
                # see if this table uses a UUID as the PK
                # if no ID is specified
                if (in_array($table, $this->uuid_tables)) {
                    if (!array_key_exists('id', $row)) {
                        $row['id'] = Uuid::generate()->string;
                    }
                }

                # encrypt any password fields
                if (array_key_exists('password', $row)) {
                    $row['password'] = bcrypt($row['password']);
                }

                # if any time fields are == to "now", then insert the right time
                foreach ($this->time_fields as $tf) {
                    if (array_key_exists($tf, $row) && strtolower($row[$tf]) === 'now') {
                        $row[$tf] = $this->time();
                    }
                }

                try {
                    DB::table($table)->insert($row);
                    ++$imported[$table];
                } catch (QueryException $e) {
                    if ($ignore_errors) {
                        continue;
                    }

                    throw $e;
                }
            }
        }

        return $imported;
    }
}
