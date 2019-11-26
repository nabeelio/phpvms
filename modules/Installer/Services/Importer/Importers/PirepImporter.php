<?php

namespace Modules\Installer\Services\Importer\Importers;

use App\Models\Enums\FlightType;
use App\Models\Enums\PirepSource;
use App\Models\Pirep;
use Modules\Installer\Services\Importer\BaseImporter;

class PirepImporter extends BaseImporter
{
    public function run($start = 0)
    {
        $this->comment('--- PIREP IMPORT ---');

        $count = 0;
        foreach ($this->db->readRows('pireps', $start) as $row) {
            $pirep_id = $row->pirepid;
            $user_id = $this->idMapper->getMapping('users', $row->pilotid);
            $airline_id = $this->idMapper->getMapping('airlines', $row->code);
            $aircraft_id = $this->idMapper->getMapping('aircraft', $row->aircraft);

            $attrs = [
                'user_id'        => $user_id,
                'airline_id'     => $airline_id,
                'aircraft_id'    => $aircraft_id,
                'flight_number'  => $row->flightnum ?: '',
                'dpt_airport_id' => $row->depicao,
                'arr_airport_id' => $row->arricao,
                'block_fuel'     => $row->fuelused,
                'route'          => $row->route ?: '',
                'source_name'    => $row->source,
                'created_at'     => $this->parseDate($row->submitdate),
                'updated_at'     => $this->parseDate($row->submitdate),
            ];

            // Set the distance
            $distance = round($row->distance ?: 0, 2);
            $attrs['distance'] = $distance;
            $attrs['planned_distance'] = $distance;

            // Set the flight time properly
            $duration = $this->convertDuration($row->flighttime_stamp);
            $attrs['flight_time'] = $duration;
            $attrs['planned_flight_time'] = $duration;

            // Set how it was filed
            if (strtoupper($row->source) === 'MANUAL') {
                $attrs['source'] = PirepSource::MANUAL;
            } else {
                $attrs['source'] = PirepSource::ACARS;
            }

            // Set the flight type
            $row->flighttype = strtoupper($row->flighttype);
            if ($row->flighttype === 'P') {
                $attrs['flight_type'] = FlightType::SCHED_PAX;
            } elseif ($row->flighttype === 'C') {
                $attrs['flight_type'] = FlightType::SCHED_CARGO;
            } else {
                $attrs['flight_type'] = FlightType::CHARTER_PAX_ONLY;
            }

            // Set the flight level of the PIREP is set
            if (property_exists($row, 'flightlevel')) {
                $attrs['level'] = $row->flightlevel;
            } else {
                $attrs['level'] = 0;
            }

            $pirep = Pirep::updateOrCreate(['id' => $pirep_id], $attrs);

            $source = strtoupper($row->source);
            if ($source === 'SMARTCARS') {
                // TODO: Parse smartcars log into the acars table
            } elseif ($source === 'KACARS') {
                // TODO: Parse kACARS log into acars table
            } elseif ($source === 'XACARS') {
                // TODO: Parse XACARS log into acars table
            }

            // TODO: Add extra fields in as PIREP fields
            $this->idMapper->addMapping('pireps', $row->pirepid, $pirep->id);

            if ($pirep->wasRecentlyCreated) {
                $count++;
            }
        }

        $this->info('Imported '.$count.' pireps');
    }
}
