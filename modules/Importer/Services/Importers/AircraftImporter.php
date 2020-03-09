<?php

namespace Modules\Importer\Services\Importers;

use App\Models\Aircraft;
use App\Models\Airline;
use App\Models\Subfleet;
use Modules\Importer\Services\BaseImporter;

class AircraftImporter extends BaseImporter
{
    protected $table = 'aircraft';

    public function run($start = 0)
    {
        $this->comment('--- AIRCRAFT IMPORT ---');

        $fields = [
            'id',
            'icao',
            'name',
            'fullname',
            'registration',
            'enabled',
        ];

        // See if there is an airline column
        $columns = $this->db->getColumns($this->table);
        if (in_array('airline', $columns)) {
            $fields[] = 'airline';
        }

        if (in_array('location', $columns)) {
            $fields[] = 'location';
        }

        $count = 0;
        $rows = $this->db->readRows($this->table, $this->idField, $start, $fields);
        foreach ($rows as $row) {
            $airline_id = null;
            if (!empty($row->airline)) {
                $airline_id = $this->idMapper->getMapping('airlines', $row->airline);
            }

            $subfleet = $this->getSubfleet($row->icao, $airline_id);
            $this->info('Subfleet ID is '.$subfleet->id);

            $where = [
                'registration' => $row->registration,
            ];

            $cols = [
                'icao'        => $row->icao,
                'name'        => $row->fullname,
                'subfleet_id' => $subfleet->id,
                'active'      => $row->enabled,
            ];

            if (!empty($row->location)) {
                $cols['airport_id'] = $row->location;
            }

            $aircraft = Aircraft::firstOrCreate($where, $cols);

            $this->idMapper->addMapping('aircraft', $row->id, $aircraft->id);

            if ($aircraft->wasRecentlyCreated) {
                $count++;
            }
        }

        $this->info('Imported '.$count.' aircraft');
    }

    /**
     * Return the subfleet
     *
     * @param string $icao       ICAO of the subfleet
     * @param int    $airline_id
     *
     * @return mixed
     */
    protected function getSubfleet($icao, $airline_id = null)
    {
        if (empty($airline_id)) {
            $airline = Airline::first();
            $airline_id = $airline->id;
        }

        return Subfleet::firstOrCreate([
            'airline_id' => $airline_id,
            'name'       => $icao,
        ], ['type' => $icao]);
    }
}
