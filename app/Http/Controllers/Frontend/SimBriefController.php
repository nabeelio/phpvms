<?php

namespace App\Http\Controllers\Frontend;

use App\Exceptions\AssetNotFound;
use App\Models\Aircraft;
use App\Models\SimBrief;
use App\Repositories\FlightRepository;
use App\Services\SimBriefService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SimBriefController
{
    private $flightRepo;
    private $simBriefSvc;

    public function __construct(FlightRepository $flightRepo, SimBriefService $simBriefSvc)
    {
        $this->flightRepo = $flightRepo;
        $this->simBriefSvc = $simBriefSvc;
    }

    /**
     * Show the main OFP form
     *
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function generate(Request $request)
    {
        $flight_id = $request->input('flight_id');
        $aircraft_id = $request->input('aircraft_id');
        $flight = $this->flightRepo->find($flight_id);
        
        if (!$flight) {
            flash()->error('Unknown flight');
            return redirect(route('frontend.flights.index'));
        }
		
        if (!$aircraft_id) {
            flash()->error('Aircraft not selected');
            return redirect(route('frontend.flights.bids'));
        }

        $apiKey = setting('simbrief.api_key');
        if (empty($apiKey)) {
            flash()->error('Invalid SimBrief API key!');
            return redirect(route('frontend.flights.index'));
        }

        $user = Auth::user();
        $simbrief = SimBrief::select('id')->where([
            'flight_id' => $flight_id,
            'user_id'   => $user->id,
        ])->first();

        if ($simbrief) {
            return redirect(route('frontend.simbrief.briefing', [$simbrief->id]));
        }
		
		if ($aircraft_id) {
			$aircraft = Aircraft::select('registration', 'name', 'icao', 'iata', 'subfleet_id')
				->where('id', $aircraft_id)
				->get();
		}

        return view('flights.simbrief_form', [
            'flight' => $flight,
            'aircraft' => $aircraft,
        ]);
    }

    /**
     * Show the briefing
     *
     * @param string $id The OFP ID
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function briefing($id)
    {
        $simbrief = SimBrief::find($id);
        if (!$simbrief) {
            flash()->error('SimBrief briefing not found');
            return redirect(route('frontend.flights.index'));
        }

        return view('flights.simbrief_briefing', [
            'simbrief' => $simbrief,
        ]);
    }

    /**
     * Create a prefile of this PIREP with a given OFP. Then redirect the
     * user to the newly prefiled PIREP
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function prefile(Request $request)
    {
        $sb = SimBrief::find($request->id);
        if (!$sb) {
            return redirect(route('frontend.flights.index'));
        }

        // Redirect to the prefile page, with the flight_id and a simbrief_id
        $rd = route('frontend.pireps.create').'?sb_id='.$sb->id;
        return redirect($rd);
    }

    /**
     * Cancel the SimBrief request
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function cancel(Request $request)
    {
        $sb = SimBrief::find($request->id);
        if (!$sb) {
            $sb->delete();
        }

        return redirect(route('frontend.simbrief.prefile', ['id' => $request->id]));
    }

    /**
     * Check whether the OFP was generated. Pass in two items, the flight_id and ofp_id
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function check_ofp(Request $request)
    {
        $ofp_id = $request->input('ofp_id');
        $flight_id = $request->input('flight_id');

        $simbrief = $this->simBriefSvc->checkForOfp(Auth::user()->id, $ofp_id, $flight_id);
        if ($simbrief === null) {
            $error = new AssetNotFound(new Exception('Simbrief OFP not found'));
            return $error->getResponse();
        }

        return response()->json([
            'id' => $simbrief->id,
        ]);
    }

    /**
     * Generate the API code
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function api_code(Request $request)
    {
        $apiKey = setting('simbrief.api_key', null);
        if (empty($apiKey)) {
            flash()->error('Invalid SimBrief API key!');
            return redirect(route('frontend.flights.index'));
        }

        $api_code = md5($apiKey.$request->input('api_req'));

        return response()->json([
            'api_code' => $api_code,
        ]);
    }
}
