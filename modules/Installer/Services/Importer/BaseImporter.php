<?php

namespace Modules\Installer\Services\Importer;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Installer\Exceptions\ImporterNextRecordSet;
use Modules\Installer\Exceptions\ImporterNoMoreRecords;
use Modules\Installer\Utils\IdMapper;
use Modules\Installer\Utils\ImporterDB;
use Modules\Installer\Utils\LoggerTrait;

abstract class BaseImporter implements ShouldQueue
{
    use LoggerTrait, Dispatchable, InteractsWithQueue, Queueable;

    protected $db;
    protected $idMapper;

    public function __construct()
    {
        $importerService = app(ImporterService::class);
        $this->db = new ImporterDB($importerService->getCredentials());
        $this->idMapper = app(IdMapper::class);
    }

    /**
     * The start method. Takes the offset to start from
     *
     * @param int $start
     *
     * @throws ImporterNoMoreRecords
     * @throws ImporterNextRecordSet
     *
     * @return mixed
     */
    abstract public function run($start = 0);

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
