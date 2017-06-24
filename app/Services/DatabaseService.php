<?php

namespace App\Services;

use Carbon\Carbon;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\DB;


class DatabaseService extends BaseService {

    protected function time(): string
    {
        return Carbon::now('UTC')->format('Y-m-d H:i:s');
    }

    public function seed_from_yaml($yaml_file)
    {
        $time_fields = ['created_at', 'updated_at'];

        $yml = Yaml::parse(file_get_contents($yaml_file));
        foreach ($yml as $table => $rows) {
            foreach ($rows as $row) {

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
