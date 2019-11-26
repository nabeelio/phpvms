<?php

namespace Modules\Installer\Exceptions;

/**
 * Signals that the stage is completed and we should go to the next one
 */
class StageCompleted extends \Exception
{
    public $nextStage;

    public function __construct($nextStage)
    {
        $this->nextStage = $nextStage;
        parent::__construct('', 0, null);
    }
}
