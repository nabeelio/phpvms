<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Controller;
use App\Events\AcarsUpdate;
use App\Exceptions\PirepCancelled;
use App\Exceptions\PirepNotFound;
use App\Http\Requests\Acars\EventRequest;
use App\Http\Requests\Acars\LogRequest;
use App\Http\Requests\Acars\PositionRequest;
use App\Http\Resources\AcarsRoute as AcarsRouteResource;
use App\Http\Resources\Pirep as PirepResource;
use App\Models\Acars;
use App\Models\Enums\AcarsType;
use App\Models\Pirep;
use App\Repositories\AcarsRepository;
use App\Repositories\PirepRepository;
use App\Services\GeoService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AcarsController extends Controller
{
    private AcarsRepository $acarsRepo;
    private GeoService $geoSvc;
    private PirepRepository $pirepRepo;

    /**
     * AcarsController constructor.
     *
     * @param AcarsRepository $acarsRepo
     * @param GeoService      $geoSvc
     * @param PirepRepository $pirepRepo
     */
    public function __construct(
        AcarsRepository $acarsRepo,
        GeoService $geoSvc,
        PirepRepository $pirepRepo
    ) {
        $this->geoSvc = $geoSvc;
        $this->acarsRepo = $acarsRepo;
        $this->pirepRepo = $pirepRepo;
    }

    /**
     * Check if a PIREP is cancelled
     *
     * @param $pirep
     *
     * @throws \App\Exceptions\PirepCancelled
     */
    protected function checkCancelled(Pirep $pirep)
    {
        if ($pirep->cancelled) {
            throw new PirepCancelled($pirep);
        }
    }

    /**
     * Get all the active PIREPs
     *
     * @return mixed
     */
    public function live_flights()
    {
        $pireps = $this->acarsRepo->getPositions(setting('acars.live_time'))->filter(
            function ($pirep) {
                return $pirep->position !== null;
            }
        );

        return PirepResource::collection($pireps);
    }

    /**
     * Return all of the flights (as points) in GeoJSON format
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function pireps_geojson(Request $request)
    {
        $pireps = $this->acarsRepo->getPositions(setting('acars.live_time'));
        $positions = $this->geoSvc->getFeatureForLiveFlights($pireps);

        return response()->json([
            'data' => $positions,
        ]);
    }

    /**
     * Return the GeoJSON for the ACARS line
     *
     * @param         $pirep_id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function acars_geojson($pirep_id, Request $request)
    {
        $pirep = Pirep::find($pirep_id);
        if (empty($pirep)) {
            throw new PirepNotFound($pirep_id);
        }

        $geodata = $this->geoSvc->getFeatureFromAcars($pirep);

        return response()->json([
            'data' => $geodata,
        ]);
    }

    /**
     * Return the routes for the ACARS line
     *
     * @param         $id
     * @param Request $request
     *
     * @return AcarsRouteResource
     */
    public function acars_get($id, Request $request)
    {
        $pirep = $this->pirepRepo->find($id);
        if (empty($pirep)) {
            throw new PirepNotFound($id);
        }

        $acars = Acars::with(['pirep'])
            ->where([
                'pirep_id' => $id,
                'type'     => AcarsType::FLIGHT_PATH,
            ])
            ->orderBy('sim_time', 'asc')
            ->get();

        return new AcarsRouteResource($acars);
    }

    /**
     * Post ACARS updates for a PIREP
     *
     * @param                 $id
     * @param PositionRequest $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \App\Exceptions\PirepCancelled
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function acars_store($id, PositionRequest $request)
    {
        // Check if the status is cancelled...
        $pirep = Pirep::find($id);
        if (empty($pirep)) {
            throw new PirepNotFound($id);
        }

        $this->checkCancelled($pirep);

        /*Log::debug(
            'Posting ACARS update (user: '.Auth::user()->ident.', pirep id :'.$id.'): ',
            $request->post()
        );*/

        $count = 0;
        $positions = $request->post('positions');
        foreach ($positions as $position) {
            $position['pirep_id'] = $id;
            $position['type'] = AcarsType::FLIGHT_PATH;

            if (isset($position['sim_time'])) {
                if ($position['sim_time'] instanceof \DateTime) {
                    $position['sim_time'] = Carbon::instance($position['sim_time']);
                } else {
                    $position['sim_time'] = Carbon::createFromTimeString($position['sim_time']);
                }
            }

            if (isset($position['created_at'])) {
                if ($position['created_at'] instanceof \DateTime) {
                    $position['created_at'] = Carbon::instance($position['created_at']);
                } else {
                    $position['created_at'] = Carbon::createFromTimeString($position['created_at']);
                }
            }

            try {
                if (isset($position['id'])) {
                    Acars::updateOrInsert(
                        ['id' => $position['id']],
                        $position
                    );
                } else {
                    $update = Acars::create($position);
                    $update->save();
                }

                $count++;
            } catch (QueryException $ex) {
                Log::info('Error on adding ACARS position: '.$ex->getMessage());
            }
        }

        // Change the PIREP status if it's as SCHEDULED before
        /*if ($pirep->status === PirepStatus::INITIATED) {
            $pirep->status = PirepStatus::AIRBORNE;
        }*/

        $pirep->save();

        // Post a new update for this ACARS position
        event(new AcarsUpdate($pirep, $pirep->position));

        return $this->message($count.' positions added', $count);
    }

    /**
     * Post ACARS LOG update for a PIREP. These updates won't show up on the map
     * But rather in a log file.
     *
     * @param            $id
     * @param LogRequest $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \App\Exceptions\PirepCancelled
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function acars_logs($id, LogRequest $request)
    {
        // Check if the status is cancelled...
        $pirep = Pirep::find($id);
        if (empty($pirep)) {
            throw new PirepNotFound($id);
        }

        $this->checkCancelled($pirep);

        // Log::debug('Posting ACARS log, PIREP: '.$id, $request->post());

        $count = 0;
        $logs = $request->post('logs');
        foreach ($logs as $log) {
            $log['pirep_id'] = $id;
            $log['type'] = AcarsType::LOG;

            if (isset($log['sim_time'])) {
                $log['sim_time'] = Carbon::createFromTimeString($log['sim_time']);
            }

            if (isset($log['created_at'])) {
                $log['created_at'] = Carbon::createFromTimeString($log['created_at']);
            }

            try {
                if (isset($log['id'])) {
                    Acars::updateOrInsert(
                        ['id' => $log['id']],
                        $log
                    );
                } else {
                    $acars = Acars::create($log);
                    $acars->save();
                }

                $count++;
            } catch (QueryException $ex) {
                Log::info('Error on adding ACARS position: '.$ex->getMessage());
            }
        }

        return $this->message($count.' logs added', $count);
    }

    /**
     * Post ACARS LOG update for a PIREP. These updates won't show up on the map
     * But rather in a log file.
     *
     * @param              $id
     * @param EventRequest $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \App\Exceptions\PirepCancelled
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function acars_events($id, EventRequest $request)
    {
        // Check if the status is cancelled...
        $pirep = Pirep::find($id);
        if (empty($pirep)) {
            throw new PirepNotFound($id);
        }

        $this->checkCancelled($pirep);

        Log::debug('Posting ACARS event, PIREP: '.$id, $request->post());

        $count = 0;
        $logs = $request->post('events');
        foreach ($logs as $log) {
            $log['pirep_id'] = $id;
            $log['type'] = AcarsType::LOG;
            $log['log'] = $log['event'];

            if (isset($log['sim_time'])) {
                $log['sim_time'] = Carbon::createFromTimeString($log['sim_time']);
            }

            if (isset($log['created_at'])) {
                $log['created_at'] = Carbon::createFromTimeString($log['created_at']);
            }

            try {
                if (isset($log['id'])) {
                    Acars::updateOrInsert(
                        ['id' => $log['id']],
                        $log
                    );
                } else {
                    $acars = Acars::create($log);
                    $acars->save();
                }

                $count++;
            } catch (QueryException $ex) {
                Log::info('Error on adding ACARS position: '.$ex->getMessage());
            }
        }

        return $this->message($count.' logs added', $count);
    }
}
