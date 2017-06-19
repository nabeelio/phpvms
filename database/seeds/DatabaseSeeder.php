<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seed_from_yaml(App::environment());
    }

    protected function seed_from_yaml($env)
    {
        $path = database_path('seeds/'.$env.'.yml');

        $time_fields = ['created_at', 'updated_at'];
        $curr_time = Carbon::now('UTC')->format('Y-m-d H:i:s');

        $yml = Yaml::parse(file_get_contents($path));
        foreach ($yml as $table => $rows) {
            foreach ($rows as $row) {

                # encrypt any password fields
                if (array_key_exists('password', $row)) {
                    $row['password'] = bcrypt($row['password']);
                }

                # if any time fields are == to "now", then insert the right time
                foreach ($time_fields as $tf) {
                    if (array_key_exists($tf, $row) && $row[$tf] === 'now') {
                        $row[$tf] = $curr_time;
                    }
                }

                DB::table($table)->insert($row);
            }
        }
    }

}
