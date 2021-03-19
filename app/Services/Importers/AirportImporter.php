<?php

namespace App\Services\Importers;

use App\Models\Airport;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class AirportImporter extends BaseImporter
{
    protected $table = 'airports';

    public function run($start = 0)
    {
        $this->comment('--- AIRPORT IMPORT ---');

        $fields = [
            'icao',
            'name',
            'country',
            'lat',
            'lng',
            'hub',
            'ground_handling_cost',
            'fuel_jeta_cost',
        ];

        $count = 0;
        $rows = $this->db->readRows($this->table, $this->idField, $start, $fields);
        foreach ($rows as $row) {
            $ground_handling_cost = (float) $row->ground_handling_cost;
            $fuel_jetA_cost = (float) $row->fuel_jeta_cost;

            if (empty($ground_handling_cost)) {
                $ground_handling_cost = 0;
            }

            if (empty($fuel_jetA_cost)) {
                $fuel_jetA_cost = 0;
            }

            $attrs = [
                'id'                   => trim($row->icao),
                'icao'                 => trim($row->icao),
                'name'                 => $row->name,
                'country'              => $row->country,
                'lat'                  => $row->lat,
                'lon'                  => $row->lng,
                'hub'                  => $row->hub,
                'ground_handling_cost' => $ground_handling_cost,
                'fuel_jeta_cost'       => $fuel_jetA_cost,
            ];

            $w = ['id' => $attrs['id']];
            //$airport = Airport::updateOrCreate($w, $attrs);

            try {
                $airport = Airport::create(array_merge($w, $attrs));
            } catch (QueryException $e) {
                $sqlState = $e->errorInfo[0];
                $errorCode = $e->errorInfo[1];
                if ($sqlState === '23000' && $errorCode === 1062) {
                    Log::info('Found duplicate for '.$row->icao.', ignoring');
                    return true;
                }

                return false;
            }

            if ($airport->wasRecentlyCreated) {
                $count++;
            }
        }

        $this->info('Imported '.$count.' airports');
        return true;
    }
}
