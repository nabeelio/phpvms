<?php

namespace Modules\Installer\Services\Importer\Importers;

use App\Models\Airline;
use Illuminate\Support\Facades\Log;
use Modules\Installer\Exceptions\ImporterNoMoreRecords;
use Modules\Installer\Services\Importer\BaseImporter;

class AirlineImporter extends BaseImporter
{
    /**
     * @param int $start
     *
     * @throws \Modules\Installer\Exceptions\ImporterNoMoreRecords
     */
    public function run($start = 0)
    {
        $this->comment('--- AIRLINE IMPORT ---');

        $count = 0;
        foreach ($this->db->readRows('airlines', $start) as $row) {
            $airline = Airline::firstOrCreate(['icao' => $row->code], [
                    'iata'   => $row->code,
                    'name'   => $row->name,
                    'active' => $row->enabled,
                ]);

            $this->idMapper->addMapping('airlines', $row->id, $airline->id);
            $this->idMapper->addMapping('airlines', $row->code, $airline->id);

            Log::debug('Mapping '.$row->id.'/'.$row->code.' to ID '.$airline->id);

            if ($airline->wasRecentlyCreated) {
                $count++;
            }
        }

        $this->info('Imported '.$count.' airlines');

        throw new ImporterNoMoreRecords();
    }
}
