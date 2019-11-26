<?php

namespace Modules\Installer\Services\Importer;

use App\Contracts\Service;
use App\Repositories\KvpRepository;
use Illuminate\Http\Request;
use Modules\Installer\Exceptions\ImporterNextRecordSet;
use Modules\Installer\Exceptions\StageCompleted;
use Modules\Installer\Utils\IdMapper;
use Modules\Installer\Utils\ImporterDB;

class ImporterService extends Service
{
    private $CREDENTIALS_KEY = 'legacy.importer.db';

    /**
     * @var KvpRepository
     */
    private $kvpRepo;

    /**
     * Hold some of our data on disk for the migration
     *
     * @var IdMapper
     */
    private $idMapper;

    /**
     * Hold the PDO connection to the old database
     *
     * @var ImporterDB
     */
    private $db;

    public function __construct()
    {
        $this->idMapper = app(IdMapper::class);
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
            'admin_email'  => $request->post('email'),
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
     * Run a given stage
     *
     * @param     $stage
     * @param int $start
     *
     * @throws ImporterNextRecordSet
     * @throws StageCompleted
     *
     * @return int|void
     */
    public function run($stage, $start = 0)
    {
        $db = new ImporterDB($this->kvpRepo->get($this->CREDENTIALS_KEY));

        $stageKlass = config('installer.importer.stages.'.$stage);

        /** @var $stage \Modules\Installer\Services\Importer\BaseStage */
        $stage = new $stageKlass($db, $this->idMapper);
        $stage->run($start);
    }
}
