<?php

namespace Modules\Importer\Services\Importers;

use App\Models\Aircraft;
use App\Models\Airline;
use App\Models\Subfleet;
use Modules\Importer\Services\BaseImporter;

class AircraftImporter extends BaseImporter
{
    protected $table = 'aircraft';

    /**
     * CONSTANTS
     */
    public const SUBFLEET_NAME = 'Imported Aircraft';

    public function run($start = 0)
    {
        $this->comment('--- AIRCRAFT IMPORT ---');

        $subfleet = $this->getSubfleet();

        $this->info('Subfleet ID is '.$subfleet->id);

        $count = 0;
        $rows = $this->db->readRows($this->table, $this->idField, $start);
        foreach ($rows as $row) {
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
