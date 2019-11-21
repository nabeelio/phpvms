<?php

namespace Modules\Installer\Services\Importer\Stages;

use Modules\Installer\Services\Importer\BaseStage;
use Modules\Installer\Services\Importer\Importers\FlightImporter;

class Stage3 extends BaseStage
{
    public $importers = [
        FlightImporter::class,
    ];
}
