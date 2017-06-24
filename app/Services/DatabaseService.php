<?php

namespace App\Services;

use Carbon\Carbon;
use Webpatser\Uuid\Uuid;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\DB;


class DatabaseService extends BaseService
{

    protected $uuid_tables = [
        'flights',
        'pireps',
        'users',
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
        $time_fields = ['created_at', 'updated_at'];
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
                foreach($time_fields as $tf) {
                    if(array_key_exists($tf, $row) && $row[$tf] === 'now') {
                        $row[$tf] = $this->time();
                    }
                }

                DB::table($table)->insert($row);
            }
        }
    }
}
