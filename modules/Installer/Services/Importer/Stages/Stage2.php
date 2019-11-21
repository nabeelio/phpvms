<?php

namespace Modules\Installer\Services\Importer\Stages;

use Modules\Installer\Services\Importer\BaseStage;
use Modules\Installer\Services\Importer\Importers\UserImport;

class Stage2 extends BaseStage
{
    public $importers = [
        UserImport::class,
    ];
}
