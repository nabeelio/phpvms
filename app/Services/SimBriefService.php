<?php

namespace App\Services;

use App\Contracts\Service;
use App\Models\Acars;
use App\Models\Enums\AcarsType;
use App\Models\Pirep;
use App\Models\SimBrief;
use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class SimBriefService extends Service
{
    private $httpClient;

    public function __construct(GuzzleClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Check to see if the OFP exists server-side. If it does, download it and
     * cache it immediately
     *
     * @param string $user_id   User who generated this
     * @param string $ofp_id    The SimBrief OFP ID
     * @param string $flight_id The flight ID
     *
     * @return SimBrief|null
     */
    public function checkForOfp(string $user_id, string $ofp_id, string $flight_id): SimBrief
    {
        $uri = 'http://www.simbrief.com/ofp/flightplans/xml/'.$ofp_id.'.xml';
        $opts = [
            'connect_timeout' => 2, // wait two seconds by default
            'allow_redirects' => false,
        ];

        try {
            $response = $this->httpClient->request('GET', $uri, $opts);
            if ($response->getStatusCode() === 404 || $response->getStatusCode() === 302) {
                return null;
            }
        } catch (GuzzleException $e) {
            Log::error('Simbrief HTTP Error: '.$e->getMessage());
            return null;
        }

        $attrs = [
            'user_id'   => $user_id,
            'flight_id' => $flight_id,
            'ofp_xml'   => $response->getBody()->getContents(),
        ];

        // Save this into the Simbrief table, if it doesn't already exist
        return SimBrief::updateOrCreate(
            ['id' => $ofp_id],
            $attrs
        );
    }

    /**
     * Create a prefiled PIREP from a given brief.
     *
     * 1. Read from the XML the basic PIREP info (dep, arr), and then associate the PIREP
     *    to the flight ID
     * 2. Remove the flight ID from the SimBrief field and assign the pirep_id to the row
     * 3. Update the planned flight route in the acars table
     * 4. Add additional flight fields (ones which match ACARS)
     *
     * @param          $pirep
     * @param SimBrief $simBrief The briefing to create the PIREP from
     *
     * @return \App\Models\Pirep
     */
    public function attachSimbriefToPirep($pirep, SimBrief $simBrief): Pirep
    {
        $this->addRouteToPirep($pirep, $simBrief);

        $simBrief->pirep_id = $pirep->id;
        $simBrief->flight_id = null;
        $simBrief->save();

        return $pirep;
    }

    /**
     * Add the route from a SimBrief flight plan to a PIREP
     *
     * @param Pirep    $pirep
     * @param SimBrief $simBrief
     *
     * @return Pirep
     */
    protected function addRouteToPirep($pirep, SimBrief $simBrief): Pirep
    {
        // Clear previous entries
        Acars::where(['pirep_id' => $pirep->id, 'type' => AcarsType::ROUTE])->delete();

        // Create the flight route
        $order = 1;
        foreach ($simBrief->xml->getRoute() as $fix) {
            $position = [
                'name'     => $fix->ident,
                'pirep_id' => $pirep->id,
                'type'     => AcarsType::ROUTE,
                'order'    => $order++,
                'lat'      => $fix->pos_lat,
                'lon'      => $fix->pos_long,
            ];

            $acars = new Acars($position);
            $acars->save();
        }

        return $pirep;
    }

    /**
     * Remove any expired entries from the SimBrief table. Expired means there's
     * a flight_id attached to it, but no pirep_id (meaning it was never used for
     * an actual flight)
     */
    public function removeExpiredEntries(): void
    {
        $expire_days = setting('simbrief.expire_days', 5);
        $expire_time = Carbon::now('UTC')->subDays($expire_days)->toDateTimeString();

        $briefs = SimBrief::where([
            ['pirep_id', '=', ''],
            ['created_at', '<', $expire_time],
        ])->get();

        foreach ($briefs as $brief) {
            $brief->delete();

            // TODO: Delete any assets
        }
    }
}
