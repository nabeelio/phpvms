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
use App\Models\User;
use App\Models\UserAward;
use Illuminate\Support\Facades\DB;
use Modules\Installer\Services\Importer\BaseStage;
use Modules\Installer\Services\Importer\Importers\AircraftImporter;
use Modules\Installer\Services\Importer\Importers\AirlineImporter;
use Modules\Installer\Services\Importer\Importers\AirportImporter;
use Modules\Installer\Services\Importer\Importers\GroupImporter;
use Modules\Installer\Services\Importer\Importers\RankImport;

class Stage1 extends BaseStage
{
    public $importers = [
        RankImport::class,
        AirlineImporter::class,
        AircraftImporter::class,
        AirportImporter::class,
        GroupImporter::class,
    ];

    public function run()
    {
        $this->cleanupDb();

        // Run the first set of importers
        parent::run();
    }

    /**
     * Cleanup the local database of any users and other data that might conflict
     * before running the importer
     */
    protected function cleanupDb()
    {
        $this->info('Running database cleanup/empty before starting');

        Acars::truncate();
        Bid::truncate();
        Expense::truncate();
        Pirep::truncate();
        User::truncate();
        UserAward::truncate();
        File::truncate();
        News::truncate();
        Journal::truncate();
        JournalTransaction::truncate();
        Flight::truncate();
        FlightField::truncate();
        FlightFieldValue::truncate();

        DB::table('flight_fare')->truncate();
        DB::table('flight_subfleet')->truncate();
    }
}
