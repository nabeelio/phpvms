<?php

namespace Modules\Importer\Services;

use App\Services\Installer\LoggerTrait;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Importer\Utils\IdMapper;
use Modules\Importer\Utils\ImporterDB;

abstract class BaseImporter implements ShouldQueue
{
    use LoggerTrait, Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Holds the connection to the legacy database
     *
     * @var \Modules\Importer\Utils\ImporterDB
     */
    protected $db;

    /**
     * The mapper class used for old IDs to new IDs
     *
     * @var \Illuminate\Contracts\Foundation\Application|mixed
     */
    protected $idMapper;

    /**
     * The legacy table this importer targets
     *
     * @var string
     */
    protected $table;

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
     * @return mixed
     */
    abstract public function run($start = 0);

    /**
     * Return a manifest of the import tasks to run. Returns an array of objects,
     * which contain a start and end row
     *
     * @return array
     */
    public function getManifest(): array
    {
        $manifest = [];

        $start = 0;
        $total_rows = $this->db->getTotalRows($this->table);
        do {
            $end = $start + $this->db->batchSize;
            if ($end > $total_rows) {
                $end = $total_rows;
            }

            $idx = $start + 1;

            $manifest[] = [
                'importer' => get_class($this),
                'start'    => $start,
                'end'      => $end,
                'message'  => 'Importing '.$this->table.' ('.$idx.' - '.$end.' of '.$total_rows.')',
            ];

            $start += $this->db->batchSize;
        } while ($start < $total_rows);

        return $manifest;
    }

    /**
     * Determine what columns exist, can be used for feature testing between v2/v5
     *
     * @return array
     */
    public function getColumns(): array
    {
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
