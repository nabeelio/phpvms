<?php

namespace Modules\Installer\Services\Importer\Stages;

use Modules\Installer\Services\Importer\BaseStage;
use Modules\Installer\Services\Importer\Importers\AirportImporter;

class Stage2 extends BaseStage
{
    public $importers = [
        AirportImporter::class,
    ];

    public $nextStage = 'stage3';
}
