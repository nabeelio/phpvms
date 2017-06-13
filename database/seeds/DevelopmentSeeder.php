<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seed_from_yaml();
    }

    protected function time(): string
    {
        return Carbon::now('UTC')->format('Y-m-d H:i:s');
    }

    protected function seed_from_yaml(): void
    {
        $time_fields = ['created_at', 'updated_at'];

        $yml = Yaml::parse(file_get_contents(database_path('seeds/dev.yml')));
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
