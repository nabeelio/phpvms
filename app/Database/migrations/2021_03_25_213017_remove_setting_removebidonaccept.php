<?php

use App\Services\Installer\SeederService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RemoveSettingRemovebidonaccept extends Migration
{
    /**
     * @var SeederService $seederSvc
     */
    private $seederSvc;

    public function __construct()
    {
        $this->seederSvc = app(SeederService::class);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('settings')
            ->where(['key' => 'pireps.remove_bid_on_accept'])
            ->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->seederSvc->syncAllSettings();
    }
}
