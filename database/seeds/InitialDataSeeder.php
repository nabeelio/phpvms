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
        # 2 main groups
        DB::table('roles')->insert(['id' => 1, 'name' => 'Administrators']);
        DB::table('roles')->insert(['id' => 2, 'name' => 'Pilots']);

        DB::table('users')->insert([
            'id'        => 1,
            'name'      => 'Admin User',
            'email'     => 'admin@phpvms.net',
            'password'  => bcrypt('admin'),
        ]);

        DB::table('role_user')->insert([
            'user_id' => 1, 'role_id' => 1
        ]);
    }
}
