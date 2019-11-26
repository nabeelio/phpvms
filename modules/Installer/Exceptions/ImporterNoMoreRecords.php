<?php

namespace Modules\Installer\Exceptions;

class ImporterNoMoreRecords extends \Exception
{
    public function __construct()
    {
        parent::__construct("", 0, null);
    }
}
