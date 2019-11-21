<?php

namespace Modules\Installer\Services\Importer\Importers;

use App\Models\Airline;
use Modules\Installer\Services\Importer\BaseImporter;

class AirlineImporter extends BaseImporter
{
    public function run()
    {
        $this->comment('--- AIRLINE IMPORT ---');

        $count = 0;
        foreach ($this->db->readRows('airlines') as $row) {
            $airline = Airline::firstOrCreate(['icao' => $row->code], [
                    'iata'   => $row->code,
                    'name'   => $row->name,
                    'active' => $row->enabled,
                ]);

            $this->idMapper->addMapping('airlines', $row->id, $airline->id);
            $this->idMapper->addMapping('airlines', $row->code, $airline->id);

            if ($airline->wasRecentlyCreated) {
                $count++;
            }
        }

        $this->info('Imported '.$count.' airlines');
    }
}
