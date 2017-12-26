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
    protected function startPirep($flight)
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
     * Parse this file and run the updates
     * @param array $files
     */
    protected function runUpdates(array $files)
    {
        /**
         * @var $flights Collection
         */
        $flights = collect($files)->transform(function ($f) {
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
        $flights->each(function ($updates, $idx) {
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
            $flights = $flights->each(function ($updates, $idx) {
                $update = $updates->shift();

            })->filter(function ($updates, $idx) {
                return $updates->count() > 0;
            });
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
