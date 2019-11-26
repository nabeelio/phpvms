<?php

namespace Modules\Installer\Services\Importer;

use App\Repositories\KvpRepository;
use Illuminate\Support\Facades\Log;
use Modules\Installer\Exceptions\ImporterNextRecordSet;
use Modules\Installer\Exceptions\ImporterNoMoreRecords;
use Modules\Installer\Exceptions\StageCompleted;
use Modules\Installer\Utils\IdMapper;
use Modules\Installer\Utils\ImporterDB;
use Modules\Installer\Utils\LoggerTrait;

class BaseStage
{
    use LoggerTrait;

    public $importers = [];
    public $nextStage = '';

    protected $db;
    protected $idMapper;

    /**
     * @var KvpRepository
     */
    protected $kvpRepo;

    public function __construct(ImporterDB $db, IdMapper $mapper)
    {
        $this->db = $db;
        $this->idMapper = $mapper;

        $this->kvpRepo = app(KvpRepository::class);
    }

    /**
     * Run all of the given importers
     *
     * @param $start
     *
     * @throws ImporterNextRecordSet
     * @throws StageCompleted
     */
    public function run($start)
    {
        $importersRun = $this->kvpRepo->get('importers.run', []);

        foreach ($this->importers as $klass) {
            /** @var $importer \Modules\Installer\Services\Importer\BaseImporter */
            $importer = new $klass($this->db, $this->idMapper);

            try {
                $importer->run($start);
            } catch (ImporterNextRecordSet $e) {
                Log::info('Requesting next set of records');

                throw $e;
            } catch (ImporterNoMoreRecords $e) {
                $importersRun[] = $importer;
            }
        }

        $this->kvpRepo->save('importers.run', $importersRun);

        throw new StageCompleted($this->nextStage);
    }
}
