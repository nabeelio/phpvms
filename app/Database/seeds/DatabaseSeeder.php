<?php

use App\Services\Installer\SeederService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private $seederService;

    public function __construct()
    {
        $this->seederService = app(SeederService::class);
    }

    /**
     * Run the database seeds.
     *
     * @throws Exception
     */
    public function run()
    {
        $this->seederService->syncAllSeeds();
    }
}
