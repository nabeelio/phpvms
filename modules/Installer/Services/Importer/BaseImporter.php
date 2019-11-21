<?php

namespace Modules\Installer\Services\Importer;

use Carbon\Carbon;
use Modules\Installer\Contracts\ImporterContract;
use Modules\Installer\Utils\IdMapper;
use Modules\Installer\Utils\ImporterDB;
use Modules\Installer\Utils\LoggerTrait;

abstract class BaseImporter implements ImporterContract
{
    use LoggerTrait;

    protected $db;
    protected $idMapper;

    public function __construct(ImporterDB $db, IdMapper $mapper)
    {
        $this->db = $db;
        $this->idMapper = $mapper;
    }

    /**
     * @param $date
     *
     * @return Carbon
     */
    protected function parseDate($date)
    {
        $carbon = Carbon::parse($date);

        return $carbon;
    }

    /**
     * Take a decimal duration and convert it to minutes
     *
     * @param $duration
     *
     * @return float|int
     */
    protected function convertDuration($duration)
    {
        if (strpos($duration, '.') !== false) {
            $delim = '.';
        } elseif (strpos($duration, ':')) {
            $delim = ':';
        } else {
            // no delimiter, assume it's just a straight hour
            return (int) $duration * 60;
        }

        $hm = explode($delim, $duration);
        $hours = (int) $hm[0] * 60;
        $mins = (int) $hm[1];

        return $hours + $mins;
    }
}
