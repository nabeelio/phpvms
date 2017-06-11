<?php

use Illuminate\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->user_seeder();
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
            'password' => bcrypt('admin'),
        ]);

        DB::table('role_user')->insert([
            'user_id' => 1,
            'role_id' => 1,
        ]);
    }
}
