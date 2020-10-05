<?php

namespace App\Services\Importers;

use App\Models\Airport;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use App\Services\BaseImporter;

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
        ];

        $count = 0;
        $rows = $this->db->readRows($this->table, $this->idField, $start, $fields);
        foreach ($rows as $row) {
            $attrs = [
                'id'      => trim($row->icao),
                'icao'    => trim($row->icao),
                'name'    => $row->name,
                'country' => $row->country,
                'lat'     => $row->lat,
                'lon'     => $row->lng,
                'hub'     => $row->hub,
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
    }
}
