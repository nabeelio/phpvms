<?php

namespace Modules\Installer\Services\Importer\Importers;

use App\Models\Aircraft;
use App\Models\Airline;
use App\Models\Subfleet;
use Modules\Installer\Exceptions\ImporterNoMoreRecords;
use Modules\Installer\Services\Importer\BaseImporter;

class AircraftImporter extends BaseImporter
{
    /**
     * CONSTANTS
     */
    public const SUBFLEET_NAME = 'Imported Aircraft';

    /**
     * @param int $start
     *
     * @throws \Modules\Installer\Exceptions\ImporterNoMoreRecords
     */
    public function run($start = 0)
    {
        $this->comment('--- AIRCRAFT IMPORT ---');

        $subfleet = $this->getSubfleet();

        $this->info('Subfleet ID is '.$subfleet->id);

        $count = 0;
        foreach ($this->db->readRows('aircraft') as $row) {
            $where = [
                'name'         => $row->fullname,
                'registration' => $row->registration,
            ];

            $aircraft = Aircraft::firstOrCreate($where, [
                'icao'        => $row->icao,
                'subfleet_id' => $subfleet->id,
                'active'      => $row->enabled,
            ]);

            $this->idMapper->addMapping('aircraft', $row->id, $aircraft->id);

            if ($aircraft->wasRecentlyCreated) {
                $count++;
            }
        }

        $this->info('Imported '.$count.' aircraft');
        throw new ImporterNoMoreRecords();
    }

    /**
     * Return the subfleet
     *
     * @return mixed
     */
    protected function getSubfleet()
    {
        $airline = Airline::first();
        $subfleet = Subfleet::firstOrCreate([
            'airline_id' => $airline->id,
            'name'       => self::SUBFLEET_NAME,
        ], ['type' => 'PHPVMS']);

        return $subfleet;
    }
}
