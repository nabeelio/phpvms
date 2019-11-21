<?php

namespace Modules\Installer\Services\Importer;

use App\Contracts\Service;
use Modules\Installer\Services\Importer\Stages\Stage1;
use Modules\Installer\Services\Importer\Stages\Stage2;
use Modules\Installer\Services\Importer\Stages\Stage3;
use Modules\Installer\Services\Importer\Stages\Stage4;
use Modules\Installer\Services\Importer\Stages\Stage5;
use Modules\Installer\Utils\IdMapper;
use Modules\Installer\Utils\ImporterDB;

/**
 * Class Importer
 * TODO: Batch import
 */
class Importer extends Service
{
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
    }

    /**
     * @param array $creds Database credentials
     *
     * @return int|void
     */
    public function run($creds)
    {
        // The db credentials
        $this->db = new ImporterDB(array_merge([
            'host'         => '127.0.0.1',
            'port'         => 3306,
            'name'         => '',
            'user'         => '',
            'pass'         => '',
            'table_prefix' => '',
        ], $creds));

        (new Stage1($this->db, $this->idMapper))->run();
        (new Stage2($this->db, $this->idMapper))->run();
        (new Stage3($this->db, $this->idMapper))->run();
        (new Stage4($this->db, $this->idMapper))->run();
        (new Stage5($this->db, $this->idMapper))->run();
    }
}
