<?php

namespace Modules\Installer\Services\Importer\Stages;

use App\Models\Acars;
use App\Models\Bid;
use App\Models\Expense;
use App\Models\File;
use App\Models\Flight;
use App\Models\FlightField;
use App\Models\FlightFieldValue;
use App\Models\Journal;
use App\Models\JournalTransaction;
use App\Models\News;
use App\Models\Pirep;
use App\Models\Subfleet;
use App\Models\User;
use App\Models\UserAward;
use Illuminate\Support\Facades\DB;
use Modules\Installer\Exceptions\ImporterNextRecordSet;
use Modules\Installer\Exceptions\StageCompleted;
use Modules\Installer\Services\Importer\BaseStage;
use Modules\Installer\Services\Importer\Importers\AircraftImporter;
use Modules\Installer\Services\Importer\Importers\AirlineImporter;
use Modules\Installer\Services\Importer\Importers\GroupImporter;
use Modules\Installer\Services\Importer\Importers\RankImport;

class Stage1 extends BaseStage
{
    public $importers = [
        RankImport::class,
        AirlineImporter::class,
        AircraftImporter::class,
        GroupImporter::class,
    ];

    public $nextStage = 'stage2';

    /**
     * @param int $start Record number to start from
     *
     * @throws ImporterNextRecordSet
     * @throws StageCompleted
     */
    public function run($start = 0)
    {
        $this->cleanupDb();

        // Run the first set of importers
        parent::run($start);
    }

    /**
     * Cleanup the local database of any users and other data that might conflict
     * before running the importer
     */
    protected function cleanupDb()
    {
        $this->info('Running database cleanup/empty before starting');

        Bid::truncate();
        File::truncate();
        News::truncate();

        Expense::truncate();
        JournalTransaction::truncate();
        Journal::truncate();

        // Clear flights
        DB::table('flight_fare')->truncate();
        DB::table('flight_subfleet')->truncate();
        FlightField::truncate();
        FlightFieldValue::truncate();
        Flight::truncate();
        Subfleet::truncate();

        // Clear permissions
//        DB::table('permission_role')->truncate();
//        DB::table('permission_user')->truncate();
//        DB::table('role_user')->truncate();
//        Role::truncate();

        Acars::truncate();
        Pirep::truncate();

        UserAward::truncate();
        User::truncate();

        // Re-run the base seeds
        //$seederSvc = app(SeederService::class);
        //$seederSvc->syncAllSeeds();
    }
}
