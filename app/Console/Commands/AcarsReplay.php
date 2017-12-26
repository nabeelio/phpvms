<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

use App\Facades\Utils;

class AcarsReplay extends Command
{
    protected $signature = 'phpvms:replay {files} {--manual}';
    protected $description = 'Replay an ACARS file';

    /**
     * API Key to post as
     * @var string
     */
    protected $apiKey = 'testadminapikey';

    /**
     * For automatic updates, how many seconds to sleep between updates
     * @var int
     */
    protected $sleepTime = 10;

    /**
     * @var array key == update[callsign]
     *            value == PIREP ID
     */
    protected $pirepList = [];

    /**
     * @var Client
     */
    protected $httpClient;


    /**
     * Return an instance of an HTTP client all ready to post
     */
    public function __construct()
    {
        parent::__construct();

        $this->httpClient = new Client([
            'base_uri' => config('app.url'),
            'headers' => [
                'Authorization' => $this->apiKey,
            ]
        ]);
    }

    /**
     * Make a request to start a PIREP
     * @param \stdClass $flight
     * @return string
     */
    protected function startPirep($flight): string
    {
        # convert the planned flight time to be completely in minutes
        $pft = Utils::hoursToMinutes($flight->planned_hrsenroute,
                                     $flight->planned_minenroute);

        $response = $this->httpClient->post('/api/pirep/prefile', [
            'json' => [
                'airline_id'            => 1,
                'aircraft_id'           => 1,  # TODO: Lookup
                'dpt_airport_id'        => $flight->planned_depairport,
                'arr_airport_id'        => $flight->planned_destairport,
                'altitude'              => $flight->planned_altitude,
                'planned_flight_time'   => $pft,
                'route'                 => $flight->planned_route,
            ]
        ]);

        $body = \json_decode($response->getBody()->getContents());
        return $body->id;
    }

    /**
     * @param $pirep_id
     * @param $data
     */
    protected function postUpdate($pirep_id, $data)
    {
        $uri = '/api/pirep/' . $pirep_id . '/acars';

        $upd = [
            'log' => '',
            'lat' => $data->latitude,
            'lon' => $data->longitude,
            'heading' => $data->heading,
            'altitude' => $data->altitude,
            'gs' => $data->groundspeed,
            'transponder' => $data->transponder,
        ];

        $this->info("Update: $data->callsign, $upd[lat] x $upd[lon] \t\t"
                    . "hdg: $upd[heading]\t\talt: $upd[altitude]\t\tgs: $upd[gs]");
        /*$this->table([], [[
            $data->callsign, $upd['lat'], $upd['lon'], $upd['heading'], $upd['altitude'], $upd['gs']
        ]]);*/

        $response = $this->httpClient->post($uri, [
            'json' => $upd
        ]);

        $body = \json_decode($response->getBody()->getContents());
        return [
            $data->callsign,
            $upd['lat'],
            $upd['lon'],
            $upd['heading'],
            $upd['altitude'],
            $upd['gs']
        ];
    }

    /**
     * Parse this file and run the updates
     * @param array $files
     */
    protected function runUpdates(array $files)
    {
        /**
         * @var $flights Collection
         */
        $flights = collect($files)->transform(function ($f)
        {
            $file = storage_path('/replay/' . $f . '.json');
            if (file_exists($file)) {
                $this->info('Loading ' . $file);
                $contents = file_get_contents($file);
                $contents = \json_decode($contents);
                return collect($contents->updates);
            } else {
                $this->error($file . ' not found, skipping');
                return false;
            }
        })
            # remove any of errored file entries
            ->filter(function ($value, $key) {
                return $value !== false;
            });

        $this->info('Starting playback');

        /**
         * File the initial pirep to get a "preflight" status
         */
        $flights->each(function ($updates, $idx)
        {
            $update = $updates->first();
            $pirep_id = $this->startPirep($update);
            $this->pirepList[$update->callsign] = $pirep_id;
            $this->info('Prefiled ' . $update->callsign . ', ID: ' . $pirep_id);
        });

        /**
         * Iterate through all of the flights, retrieving the updates
         * from each individual flight. Remove the update. Continue through
         * until there are no updates left, at which point we remove the flight
         * and updates.
         *
         * Continue until we have no more flights and updates left
         */
        while ($flights->count() > 0) {
            $updated_rows = [];
            $flights = $flights->each(function ($updates, $idx)
            {
                $update = $updates->shift();
                $pirep_id = $this->pirepList[$update->callsign];

                $row = $this->postUpdate($pirep_id, $update);
                $updated_rows[] = $row;
            })->filter(function ($updates, $idx) {
                return $updates->count() > 0;
            });

            /*$this->table(
                ['callsign', 'lat', 'lon', 'hdg', 'alt', 'gs'],
                $updated_rows);*/

            if(!$this->option('manual')) {
                sleep($this->sleepTime);
            } else {
                $this->confirm('Send next batch of updates?', true);
            }
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $files = $this->argument('files');
        $manual_mode = $this->option('manual');

        if(!$manual_mode) {
            $this->info('Going to send updates every 10s');
        } else {
            $this->info('In "manual advance" mode');
        }

        $this->runUpdates(explode(',', $files));
        $this->info('Done!');
    }
}
