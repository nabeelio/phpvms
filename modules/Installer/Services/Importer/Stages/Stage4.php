<?php

namespace Modules\Installer\Services\Importer\Stages;

use Modules\Installer\Services\Importer\BaseStage;
use Modules\Installer\Services\Importer\Importers\PirepImporter;

class Stage4 extends BaseStage
{
    public $importers = [
        PirepImporter::class,
    ];
}
