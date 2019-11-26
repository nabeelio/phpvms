<?php

namespace Modules\Installer\Services\Importer\Importers;

use App\Models\Airport;
use Modules\Installer\Exceptions\ImporterNoMoreRecords;
use Modules\Installer\Services\Importer\BaseImporter;

class AirportImporter extends BaseImporter
{
    /**
     * @param int $start
     *
     * @throws \Modules\Installer\Exceptions\ImporterNoMoreRecords
     */
    public function run($start = 0)
    {
        $this->comment('--- AIRPORT IMPORT ---');

        $count = 0;
        foreach ($this->db->readRows('airports', $start) as $row) {
            $attrs = [
                'id'      => trim($row->icao),
                'icao'    => trim($row->icao),
                'name'    => $row->name,
                'country' => $row->country,
                'lat'     => $row->lat,
                'lon'     => $row->lng,
                'hub'     => $row->hub,
            ];

            $airport = Airport::updateOrCreate(['id' => $attrs['id']], $attrs);

            if ($airport->wasRecentlyCreated) {
                $count++;
            }
        }

        $this->info('Imported '.$count.' airports');
        throw new ImporterNoMoreRecords();
    }
}
