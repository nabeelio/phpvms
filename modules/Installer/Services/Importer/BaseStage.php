<?php

namespace Modules\Installer\Services\Importer;

use Modules\Installer\Utils\IdMapper;
use Modules\Installer\Utils\ImporterDB;
use Modules\Installer\Utils\LoggerTrait;

class BaseStage
{
    use LoggerTrait;

    public $importers = [];

    protected $db;
    protected $idMapper;

    public function __construct(ImporterDB $db, IdMapper $mapper)
    {
        $this->db = $db;
        $this->idMapper = $mapper;
    }

    /**
     * Run all of the given importers
     */
    public function run()
    {
        foreach ($this->importers as $klass) {
            $importer = new $klass($this->db, $this->idMapper);
            $importer->run();
        }
    }
}
