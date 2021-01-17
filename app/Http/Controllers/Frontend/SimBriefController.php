<?php

namespace App\Http\Controllers\Frontend;

use App\Exceptions\AssetNotFound;
use App\Models\Aircraft;
use App\Models\Enums\FlightType;
use App\Models\SimBrief;
use App\Repositories\FlightRepository;
use App\Services\FareService;
use App\Services\SimBriefService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SimBriefController
{
    private $fareSvc;
    private $flightRepo;
    private $simBriefSvc;
    private $userSvc;

    public function __construct(
        FareService $fareSvc,
        FlightRepository $flightRepo,
        SimBriefService $simBriefSvc,
        UserService $userSvc
    ) {
        $this->fareSvc = $fareSvc;
        $this->flightRepo = $flightRepo;
        $this->simBriefSvc = $simBriefSvc;
        $this->userSvc = $userSvc;
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
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $flight_id = $request->input('flight_id');
        $aircraft_id = $request->input('aircraft_id');
        $flight = $this->flightRepo->with(['subfleets'])->find($flight_id);
        $flight = $this->fareSvc->getReconciledFaresForFlight($flight);

        if (!$flight) {
            flash()->error('Unknown flight');
            return redirect(route('frontend.flights.index'));
        }

        if (!$aircraft_id) {
            flash()->error('Aircraft not selected ! Please select an Aircraft to Proceed ...');
        }

        $apiKey = setting('simbrief.api_key');
        if (empty($apiKey)) {
            flash()->error('Invalid SimBrief API key!');
            return redirect(route('frontend.flights.index'));
        }

        $simbrief = SimBrief::select('id')->where([
            'flight_id' => $flight_id,
            'user_id'   => $user->id,
        ])->first();

        if ($simbrief) {
            return redirect(route('frontend.simbrief.briefing', [$simbrief->id]));
        }

        $aircraft = Aircraft::select('registration', 'name', 'icao', 'iata', 'subfleet_id')
            ->where('id', $aircraft_id)
            ->get();

        if ($flight->subfleets->count() > 0) {
            $subfleets = $flight->subfleets;
        } else {
            $subfleets = $this->userSvc->getAllowableSubfleets($user);
        }

        if ($flight->flight_type === FlightType::CHARTER_PAX_ONLY) {
            $pax_weight = 197;
        } else {
            $pax_weight = 208;
        }

        return view('flights.simbrief_form', [
            'flight'     => $flight,
            'aircraft'   => $aircraft,
            'subfleets'  => $subfleets,
            'pax_weight' => $pax_weight, // TODO: Replace with a setting
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

        $str = $simbrief->xml->aircraft->equip;
        $wc = stripos($str, '-');
        $tr = stripos($str, '/');
        $wakecat = substr($str, 0, $wc);
        $equipment = substr($str, $wc + 1, $tr - 2);
        $transponder = substr($str, $tr + 1);

        return view('flights.simbrief_briefing', [
            'simbrief'    => $simbrief,
            'wakecat'     => $wakecat,
            'equipment'   => $equipment,
            'transponder' => $transponder,
        ]);
    }

    /**
     * Remove the flight_id from the SimBrief Briefing (to a create a new one)
     * or if no pirep_id is attached to the briefing delete it completely
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function remove(Request $request)
    {
        $sb_pack = SimBrief::find($request->id);
        if ($sb_pack) {
            if (!$sb_pack->pirep_id) { 
                $sb_pack->delete();
            } else {
                $sb_pack->flight_id = NULL;
                $sb_pack->save();
            }
        }

        return redirect(route('frontend.flights.index'));
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
