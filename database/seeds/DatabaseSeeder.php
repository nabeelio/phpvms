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
        $this->user_seeder();
        $this->seed_from_yaml();
        //$this->airport_seeder();
    }

    protected function time() {
        return Carbon::now()->format('Y-m-d H:i:s');
    }
    /**
     * Add an initial admin user and roles
     */
    protected function user_seeder()
    {
        foreach ([
            ['id' => 1,'name' => 'admin', 'display_name' => 'Administrators'],
            ['id' => 2, 'name' => 'user', 'display_name' => 'Pilot'],
        ] as $group) { DB::table('roles')->insert($group); }

        DB::table('users')->insert([
            'id'       => 1,
            'name'     => 'Admin User',
            'email'    => 'admin@phpvms.net',
            'password' => bcrypt('phpvms'),
            'created_at' => Carbon::now('UTC'),
            'updated_at' => Carbon::now('UTC'),
        ]);

        // add as both admin and user role
        DB::table('role_user')->insert(['user_id' => 1, 'role_id' => 1]);
        DB::table('role_user')->insert(['user_id' => 1, 'role_id' => 2]);
    }

    protected function seed_from_yaml()
    {
        $yml = Yaml::parse(file_get_contents(database_path('seeds/seed.yml')));
        foreach ($yml as $table => $rows) {
            foreach($rows as $row) {
                DB::table($table)->insert($row);
            }
        }
    }

}
