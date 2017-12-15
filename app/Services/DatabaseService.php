<?php

namespace App\Services;

use Log;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Webpatser\Uuid\Uuid;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\DB;


class DatabaseService extends BaseService
{

    protected $time_fields = [
        'created_at',
        'updated_at'
    ];

    protected $uuid_tables = [
        'flights',
        'pireps',
    ];

    protected function time(): string
    {
        return Carbon::now('UTC')->format('Y-m-d H:i:s');
    }

    public function seed_from_yaml_file($yaml_file)
    {
        $yml = file_get_contents($yaml_file);
        $this->seed_from_yaml($yml);
    }

    public function seed_from_yaml($yml)
    {
        $yml = Yaml::parse($yml);
        foreach ($yml as $table => $rows) {
            foreach ($rows as $row) {

                # see if this table uses a UUID as the PK
                # if no ID is specified
                if(in_array($table, $this->uuid_tables)) {
                    if(!array_key_exists('id', $row)) {
                        $row['id'] = Uuid::generate()->string;
                    }
                }

                # encrypt any password fields
                if(array_key_exists('password', $row)) {
                    $row['password'] = bcrypt($row['password']);
                }

                # if any time fields are == to "now", then insert the right time
                foreach($this->time_fields as $tf) {
                    if(array_key_exists($tf, $row) && $row[$tf] === 'now') {
                        $row[$tf] = $this->time();
                    }
                }

                try {
                    DB::table($table)->insert($row);
                } catch(QueryException $e) {
                    Log::info($e->getMessage());
                }
            }
        }
    }
}
