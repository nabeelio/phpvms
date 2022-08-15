<?php

namespace App\Services;

use App\Contracts\Service;
use App\Repositories\KvpRepository;
use App\Services\Importers\AircraftImporter;
use App\Services\Importers\AirlineImporter;
use App\Services\Importers\AirportImporter;
use App\Services\Importers\ClearDatabase;
use App\Services\Importers\ExpenseImporter;
use App\Services\Importers\FinalizeImporter;
use App\Services\Importers\FlightImporter;
use App\Services\Importers\GroupImporter;
use App\Services\Importers\LedgerImporter;
use App\Services\Importers\PirepImporter;
use App\Services\Importers\RankImport;
use App\Services\Importers\SettingsImporter;
use App\Services\Importers\UserImport;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ImporterService extends Service
{
    private string $CREDENTIALS_KEY = 'legacy.importer.db';

    /**
     * @var KvpRepository
     */
    private mixed $kvpRepo;

    /**
     * The list of importers, in proper order
     */
    private array $importList = [
        ClearDatabase::class,
        RankImport::class,
        GroupImporter::class,
        AirlineImporter::class,
        AircraftImporter::class,
        AirportImporter::class,
        FlightImporter::class,
        UserImport::class,
        PirepImporter::class,
        ExpenseImporter::class,
        LedgerImporter::class,
        SettingsImporter::class,
        FinalizeImporter::class,
    ];

    public function __construct()
    {
        $this->kvpRepo = app(KvpRepository::class);
    }

    /**
     * Save the credentials from a request
     *
     * @param \Illuminate\Http\Request $request
     */
    public function saveCredentialsFromRequest(Request $request)
    {
        $creds = [
            'host'         => $request->post('db_host'),
            'port'         => $request->post('db_port'),
            'name'         => $request->post('db_name'),
            'user'         => $request->post('db_user'),
            'pass'         => $request->post('db_pass'),
            'table_prefix' => $request->post('db_prefix'),
        ];

        $this->saveCredentials($creds);
    }

    /**
     * Save the given credentials
     *
     * @param array $creds
     */
    public function saveCredentials(array $creds)
    {
        $creds = array_merge([
            'admin_email'  => '',
            'host'         => '',
            'port'         => '',
            'name'         => '',
            'user'         => '',
            'pass'         => 3306,
            'table_prefix' => 'phpvms_',
        ], $creds);

        $this->kvpRepo->save($this->CREDENTIALS_KEY, $creds);
    }

    /**
     * Get the saved credentials
     */
    public function getCredentials()
    {
        return $this->kvpRepo->get($this->CREDENTIALS_KEY);
    }

    /**
     * Create a manifest of the import. Creates an array with the importer name,
     * which then has a subarray of all of the different steps/stages it needs to run
     */
    public function generateImportManifest()
    {
        $manifest = [];

        foreach ($this->importList as $importerKlass) {
            /** @var BaseImporter $importer */
            $importer = new $importerKlass();
            $manifest = array_merge($manifest, $importer->getManifest());
        }

        return $manifest;
    }

    /**
     * Run a given stage
     *
     * @param     $importer
     * @param int $start
     *
     * @throws \Exception
     *
     * @return int|void
     */
    public function run($importer, $start = 0)
    {
        if (!in_array($importer, $this->importList, true)) {
            throw new Exception('Unknown importer "'.$importer.'"');
        }

        /** @var $importerInst BaseImporter */
        $importerInst = new $importer();

        try {
            $importerInst->run($start);
        } catch (Exception $e) {
            Log::error('Error running importer: '.$e->getMessage());
        }
    }
}
