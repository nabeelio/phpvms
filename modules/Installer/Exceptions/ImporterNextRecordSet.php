<?php

namespace Modules\Installer\Exceptions;

/**
 * Go to the next page of the importer
 */
class ImporterNextRecordSet extends \Exception
{
    public $nextOffset;

    /**
     * ImporterNextRecordSet constructor.
     *
     * @param int $nextOffset Where to start the next set of reads from
     */
    public function __construct($nextOffset)
    {
        parent::__construct('', 0, null);
        $this->nextOffset = $nextOffset;
    }
}
