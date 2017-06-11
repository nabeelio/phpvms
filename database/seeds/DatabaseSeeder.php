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
        $this->airport_seeder();
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

    /**
     * Add a few initial airports
     */
    protected function airport_seeder()
    {
        $airports = [
            [
                'icao' => 'KAUS',
                'name' => 'Austin-Bergstrom International Airport',
                'location' => 'Austin, Texas, USA',
                'lat' => 30.1945278,
                'lon' => -97.6698889,
            ],
            [
                'icao' => 'KJFK',
                'name' => 'John F Kennedy International Airport',
                'location' => 'New York, New York, USA',
                'lat' => 40.6399257,
                'lon' => -73.7786950,
            ],
        ];

        foreach($airports as $airport) {
            DB::table('airports')->insert($airport);
        }
    }
}
