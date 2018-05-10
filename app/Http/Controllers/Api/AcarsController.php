<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\PirepCancelled;
use App\Http\Requests\Acars\EventRequest;
use App\Http\Requests\Acars\LogRequest;
use App\Http\Requests\Acars\PositionRequest;
use App\Http\Resources\AcarsRoute as AcarsRouteResource;
use App\Interfaces\Controller;
use App\Models\Acars;
use App\Models\Enums\AcarsType;
use App\Models\Enums\PirepStatus;
use App\Models\Pirep;
use App\Repositories\AcarsRepository;
use App\Repositories\PirepRepository;
use App\Services\GeoService;
use App\Services\PirepService;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;

/**
 * Class AcarsController
 * @package App\Http\Controllers\Api
 */
class AcarsController extends Controller
{
    private $acarsRepo,
            $geoSvc,
            $pirepRepo,
            $pirepSvc;

    /**
     * AcarsController constructor.
     * @param AcarsRepository $acarsRepo
     * @param GeoService      $geoSvc
     * @param PirepRepository $pirepRepo
     * @param PirepService    $pirepSvc
     */
    public function __construct(
        AcarsRepository $acarsRepo,
        GeoService $geoSvc,
        PirepRepository $pirepRepo,
        PirepService $pirepSvc
    ) {
        $this->geoSvc = $geoSvc;
        $this->acarsRepo = $acarsRepo;
        $this->pirepRepo = $pirepRepo;
        $this->pirepSvc = $pirepSvc;
    }

    /**
     * Check if a PIREP is cancelled
     * @param $pirep
     * @throws \App\Exceptions\PirepCancelled
     */
    protected function checkCancelled(Pirep $pirep)
    {
        if (!$pirep->allowedUpdates()) {
            throw new PirepCancelled();
        }
    }

    /**
     * Return all of the flights (as points) in GeoJSON format
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $pireps = $this->acarsRepo->getPositions();
        $positions = $this->geoSvc->getFeatureForLiveFlights($pireps);

        return response(json_encode($positions), 200, [
            'Content-type' => 'application/json'
        ]);
    }

    /**
     * Return the GeoJSON for the ACARS line
     * @param         $pirep_id
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function acars_geojson($pirep_id, Request $request)
    {
        $pirep = Pirep::find($pirep_id);
        $geodata = $this->geoSvc->getFeatureFromAcars($pirep);

        return response(\json_encode($geodata), 200, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Return the routes for the ACARS line
     * @param         $id
     * @param Request $request
     * @return AcarsRouteResource
     */
    public function acars_get($id, Request $request)
    {
        $this->pirepRepo->find($id);

        return new AcarsRouteResource(Acars::where([
            'pirep_id' => $id,
            'type'     => AcarsType::FLIGHT_PATH
        ])->orderBy('created_at', 'asc')->get());
    }

    /**
     * Post ACARS updates for a PIREP
     * @param                 $id
     * @param PositionRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\PirepCancelled
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function acars_store($id, PositionRequest $request)
    {
        # Check if the status is cancelled...
        $pirep = Pirep::find($id);
        $this->checkCancelled($pirep);

        Log::debug(
            'Posting ACARS update (user: '.Auth::user()->pilot_id.', pirep id :'.$id.'): ',
            $request->post()
        );

        $count = 0;
        $positions = $request->post('positions');
        foreach ($positions as $position) {
            $position['pirep_id'] = $id;
            $position['type'] = AcarsType::FLIGHT_PATH;

            if (array_key_exists('sim_time', $position)) {
                $position['sim_time'] = Carbon::createFromTimeString($position['sim_time']);
            }

            if (array_key_exists('created_at', $position)) {
                $position['created_at'] = Carbon::createFromTimeString($position['created_at']);
            }

            $update = Acars::create($position);
            $update->save();

            ++$count;
        }

        # Change the PIREP status if it's as SCHEDULED before
        if ($pirep->status === PirepStatus::INITIATED) {
            $pirep->status = PirepStatus::AIRBORNE;
        }

        $pirep->save();

        return $this->message($count.' positions added', $count);
    }

    /**
     * Post ACARS LOG update for a PIREP. These updates won't show up on the map
     * But rather in a log file.
     * @param            $id
     * @param LogRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\PirepCancelled
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function acars_logs($id, LogRequest $request)
    {
        # Check if the status is cancelled...
        $pirep = Pirep::find($id);
        $this->checkCancelled($pirep);

        Log::debug('Posting ACARS log, PIREP: '.$id, $request->post());

        $count = 0;
        $logs = $request->post('logs');
        foreach ($logs as $log) {
            $log['pirep_id'] = $id;
            $log['type'] = AcarsType::LOG;

            if (array_key_exists('sim_time', $log)) {
                $log['sim_time'] = Carbon::createFromTimeString($log['sim_time']);
            }

            if (array_key_exists('created_at', $log)) {
                $log['created_at'] = Carbon::createFromTimeString($log['created_at']);
            }

            $acars = Acars::create($log);
            $acars->save();
            ++$count;
        }

        return $this->message($count.' logs added', $count);
    }

    /**
     * Post ACARS LOG update for a PIREP. These updates won't show up on the map
     * But rather in a log file.
     * @param              $id
     * @param EventRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\PirepCancelled
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function acars_events($id, EventRequest $request)
    {
        # Check if the status is cancelled...
        $pirep = Pirep::find($id);
        $this->checkCancelled($pirep);

        Log::debug('Posting ACARS event, PIREP: '.$id, $request->post());

        $count = 0;
        $logs = $request->post('events');
        foreach ($logs as $log) {
            $log['pirep_id'] = $id;
            $log['type'] = AcarsType::LOG;
            $log['log'] = $log['event'];

            if (array_key_exists('sim_time', $log)) {
                $log['sim_time'] = Carbon::createFromTimeString($log['sim_time']);
            }

            if (array_key_exists('created_at', $log)) {
                $log['created_at'] = Carbon::createFromTimeString($log['created_at']);
            }

            $acars = Acars::create($log);
            $acars->save();
            ++$count;
        }

        return $this->message($count.' logs added', $count);
    }
}
