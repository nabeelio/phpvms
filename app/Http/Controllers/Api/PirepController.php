<?php

namespace App\Http\Controllers\Api;

use Log;
use Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Models\Acars;
use App\Models\Pirep;

use App\Models\Enums\AcarsType;
use App\Models\Enums\PirepState;
use App\Models\Enums\PirepStatus;

use App\Services\GeoService;
use App\Services\PIREPService;
use App\Repositories\AcarsRepository;
use App\Repositories\PirepRepository;

use App\Http\Resources\Acars as AcarsResource;
use App\Http\Resources\Pirep as PirepResource;
use App\Http\Resources\AcarsLog as AcarsLogResource;
use App\Http\Resources\AcarsRoute as AcarsRouteResource;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PirepController extends RestController
{
    public static $acars_rules = [
        'altitude',
        'level',
        'heading',
        'vs',
        'gs',
        'transponder',
        'autopilot',
        'fuel_flow',
        'log',
        'lat',
        'lon',
        'created_at',
    ];

    public static $pirep_rules = [
        'airline_id',
        'aircraft_id',
        'dpt_airport_id',
        'arr_airport_id',
        'flight_id',
        'flight_number',
        'route_code',
        'route_leg',
        'flight_time',
        'planned_flight_time',
        'level',
        'route',
        'notes',
    ];

    protected $acarsRepo,
        $geoSvc,
        $pirepRepo,
        $pirepSvc;

    /**
     * PirepController constructor.
     * @param AcarsRepository $acarsRepo
     * @param GeoService $geoSvc
     * @param PirepRepository $pirepRepo
     * @param PIREPService $pirepSvc
     */
    public function __construct(
        AcarsRepository $acarsRepo,
        GeoService $geoSvc,
        PirepRepository $pirepRepo,
        PIREPService $pirepSvc
    ) {
        $this->acarsRepo = $acarsRepo;
        $this->geoSvc = $geoSvc;
        $this->pirepRepo = $pirepRepo;
        $this->pirepSvc = $pirepSvc;
    }

    public function get($id)
    {
        PirepResource::withoutWrapping();
        return new PirepResource($this->pirepRepo->find($id));
    }

    /**
     * Create a new PIREP and place it in a "inprogress" and "prefile" state
     * Once ACARS updates are being processed, then it can go into an 'ENROUTE'
     * status, and whatever other statuses may be defined
     *
     * TODO: Allow extra fields, etc to be set. Aircraft, etc
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function prefile(Request $request)
    {
        Log::info('PIREP Prefile, user '.Auth::user()->id, $request->toArray());

        $attrs = $this->getFromReq($request, self::$pirep_rules, [
            'user_id' => Auth::user()->id,
            'state' => PirepState::IN_PROGRESS,
            'status' => PirepStatus::PREFILE,
        ]);

        $pirep = new Pirep($attrs);

        # Find if there's a duplicate, if so, let's work on that
        $dupe_pirep = $this->pirepSvc->findDuplicate($pirep);
        if($dupe_pirep !== false) {
            $pirep = $dupe_pirep;
        }

        $pirep->save();

        $this->pirepSvc->saveRoute($pirep);

        Log::info('PIREP PREFILED');
        Log::info($pirep->id);

        PirepResource::withoutWrapping();
        return new PirepResource($pirep);
    }

    /**
     * File the PIREP
     * @param $id
     * @param Request $request
     * @return PirepResource
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function file($id, Request $request)
    {
        Log::info('PIREP Prefile, user ' . Auth::user()->pilot_id, $request->toArray());

        $pirep = $this->pirepRepo->find($id);
        if (empty($pirep)) {
            throw new ModelNotFoundException('PIREP not found');
        }

        # Check if the status is cancelled...
        if($pirep->state === PirepState::CANCELLED) {
            throw new BadRequestHttpException('PIREP has been cancelled, updates can\'t be posted');
        }

        $attrs = $this->getFromReq($request, self::$pirep_rules, [
            'state' => PirepState::PENDING,
            'status' => PirepStatus::ARRIVED,
        ]);

        $pirep_fields = [];
        if($request->filled('fields')) {
            $pirep_fields = $request->get('fields');
        }

        try {
            $pirep = $this->pirepRepo->update($attrs, $id);
            $pirep = $this->pirepSvc->create($pirep, $pirep_fields);
        } catch (\Exception $e) {
            Log::error($e);
        }

        PirepResource::withoutWrapping();
        return new PirepResource($pirep);
    }

    /**
     * Cancel the PIREP
     * @param $id
     * @param Request $request
     * @return PirepResource
     */
    public function cancel($id, Request $request)
    {
        Log::info('PIREP Cancel, user ' . Auth::user()->pilot_id, $request->toArray());

        $attrs = [
            'state' => PirepState::CANCELLED,
        ];

        try {
            $pirep = $this->pirepRepo->update($attrs, $id);
        } catch (\Exception $e) {
            Log::error($e);
        }

        PirepResource::withoutWrapping();
        return new PirepResource($pirep);
    }

    /**
     * Return the GeoJSON for the ACARS line
     * @param $id
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */
    public function acars_geojson($id, Request $request)
    {
        $pirep = $this->pirepRepo->find($id);
        $geodata = $this->geoSvc->getFeatureFromAcars($pirep);

        return response(\json_encode($geodata), 200, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Return the GeoJSON for the ACARS line
     * @param $id
     * @param Request $request
     * @return AcarsRouteResource
     */
    public function acars_get($id, Request $request)
    {
        $pirep = $this->pirepRepo->find($id);

        AcarsRouteResource::withoutWrapping();
        return new AcarsRouteResource(Acars::where([
            'pirep_id' => $id,
            'type' => AcarsType::FLIGHT_PATH
        ])->orderBy('created_at', 'asc')->get());
    }

    /**
     * Post ACARS updates for a PIREP
     * @param $id
     * @param Request $request
     * @return AcarsRouteResource
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function acars_store($id, Request $request)
    {
        $pirep = $this->pirepRepo->find($id);

        # Check if the status is cancelled...
        if ($pirep->state === PirepState::CANCELLED) {
            throw new BadRequestHttpException('PIREP has been cancelled, updates can\'t be posted');
        }

        Log::info('Posting ACARS update', $request->toArray());

        $this->validate($request, ['positions' => 'required']);
        $positions = $request->post()['positions'];

        foreach($positions as $position)
        {
            try {
                $attrs = $this->getFromReq(
                    $position,
                    self::$acars_rules,
                    ['pirep_id' => $id, 'type' => AcarsType::FLIGHT_PATH]
                );

                $update = Acars::create($attrs);
                $update->save();
            } catch (\Exception $e) {
                Log::error($e);
            }
        }

        # Change the PIREP status
        $pirep->status = PirepStatus::ENROUTE;
        $pirep->save();

        return $this->acars_get($id, $request);
    }

    /**
     * Post ACARS LOG update for a PIREP. These updates won't show up on the map
     * But rather in a log file.
     * @param $id
     * @param Request $request
     * @return AcarsLogResource
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function acars_log($id, Request $request)
    {
        $pirep = $this->pirepRepo->find($id);

        # Check if the status is cancelled...
        if ($pirep->state === PirepState::CANCELLED) {
            throw new BadRequestHttpException('PIREP has been cancelled, updates can\'t be posted');
        }

        Log::info('Posting ACARS log', $request->toArray());

        $attrs = $this->getFromReq($request, [
            'log' => 'required',
            'lat' => 'nullable',
            'lon' => 'nullable',
        ], ['pirep_id' => $id, 'type' => AcarsType::LOG]);

        $acars = Acars::create($attrs);
        $acars->save();

        AcarsLogResource::withoutWrapping();
        return new AcarsLogResource($acars);
    }

    /**
     * @param $id
     * @param Request $request
     * @return AcarsRouteResource
     */
    public function route_get($id, Request $request)
    {
        $this->pirepRepo->find($id);

        AcarsRouteResource::withoutWrapping();
        return new AcarsRouteResource(Acars::where([
            'pirep_id' => $id,
            'type' => AcarsType::ROUTE
        ])->orderBy('order', 'asc')->get());
    }

    /**
     * Post the ROUTE for a PIREP, can be done from the ACARS log
     * @param $id
     * @param Request $request
     * @return AcarsRouteResource
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function route_post($id, Request $request)
    {
        $pirep = $this->pirepRepo->find($id);

        # Check if the status is cancelled...
        if ($pirep->state === PirepState::CANCELLED) {
            throw new BadRequestHttpException('PIREP has been cancelled, updates can\'t be posted');
        }

        Log::info('Posting ACARS ROUTE', $request->toArray());

        $this->validate($request, [
            'route.*.name' => 'required',
            'route.*.order' => 'required|int',
            'route.*.nav_type' => 'nullable|int',
            'route.*.lat' => 'required|numeric',
            'route.*.lon' => 'required|numeric',
        ]);

        $route = $request->all()['route'];
        foreach($route as $position) {
            $attrs = [
                'pirep_id' => $id,
                'type' => AcarsType::ROUTE,
                'name' => $position['name'],
                'order' => $position['order'],
                'lat' => $position['lat'],
                'lon' => $position['lon'],
            ];

            if(array_key_exists('nav_type', $position)) {
                $attrs['nav_type'] = $position['nav_type'];
            }

            try {
                $acars = Acars::create($attrs);
                $acars->save();
            } catch (\Exception $e) {
                Log::error($e);
            }
        }

        return $this->route_get($id, $request);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function route_delete($id, Request $request)
    {
        $this->pirepRepo->find($id);

        Acars::where([
            'pirep_id' => $id,
            'type' => AcarsType::ROUTE
        ])->delete();

        return $this->message('Route deleted');
    }
}
