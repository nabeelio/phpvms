<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\AircraftPermissionDenied;
use App\Exceptions\PirepCancelled;
use App\Http\Requests\Acars\CommentRequest;
use App\Http\Requests\Acars\EventRequest;
use App\Http\Requests\Acars\FileRequest;
use App\Http\Requests\Acars\LogRequest;
use App\Http\Requests\Acars\PositionRequest;
use App\Http\Requests\Acars\PrefileRequest;
use App\Http\Requests\Acars\RouteRequest;
use App\Http\Requests\Acars\UpdateRequest;
use App\Http\Resources\AcarsRoute as AcarsRouteResource;
use App\Http\Resources\JournalTransaction as JournalTransactionResource;
use App\Http\Resources\Pirep as PirepResource;
use App\Http\Resources\PirepComment as PirepCommentResource;
use App\Models\Acars;
use App\Models\Enums\AcarsType;
use App\Models\Enums\PirepSource;
use App\Models\Enums\PirepState;
use App\Models\Enums\PirepStatus;
use App\Models\Pirep;
use App\Models\PirepComment;
use App\Repositories\AcarsRepository;
use App\Repositories\JournalRepository;
use App\Repositories\PirepRepository;
use App\Services\FareService;
use App\Services\Finance\PirepFinanceService;
use App\Services\GeoService;
use App\Services\PirepService;
use App\Services\UserService;
use Auth;
use Illuminate\Http\Request;
use Log;

class PirepController extends RestController
{
    private $acarsRepo,
            $fareSvc,
            $financeSvc,
            $geoSvc,
            $journalRepo,
            $pirepRepo,
            $pirepSvc,
            $userSvc;

    /**
     * PirepController constructor.
     * @param AcarsRepository $acarsRepo
     * @param PirepFinanceService $financeSvc
     * @param GeoService $geoSvc
     * @param JournalRepository $journalRepo
     * @param PirepRepository $pirepRepo
     * @param PirepService $pirepSvc
     * @param UserService $userSvc
     */
    public function __construct(
        AcarsRepository $acarsRepo,
        FareService $fareSvc,
        PirepFinanceService $financeSvc,
        GeoService $geoSvc,
        JournalRepository $journalRepo,
        PirepRepository $pirepRepo,
        PirepService $pirepSvc,
        UserService $userSvc
    ) {
        $this->acarsRepo = $acarsRepo;
        $this->fareSvc = $fareSvc;
        $this->financeSvc = $financeSvc;
        $this->geoSvc = $geoSvc;
        $this->journalRepo = $journalRepo;
        $this->pirepRepo = $pirepRepo;
        $this->pirepSvc = $pirepSvc;
        $this->userSvc = $userSvc;
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
     * @param $id
     * @return PirepResource
     */
    public function get($id)
    {
        return new PirepResource($this->pirepRepo->find($id));
    }

    /**
     * @param $pirep
     * @param Request $request
     */
    protected function updateFields($pirep, Request $request)
    {
        if (!$request->filled('fields')) {
            return;
        }

        $pirep_fields = [];
        foreach ($request->input('fields') as $field_name => $field_value) {
            $pirep_fields[] = [
                'name' => $field_name,
                'value' => $field_value,
                'source' => $pirep->source,
            ];
        }

        $this->pirepSvc->updateCustomFields($pirep->id, $pirep_fields);
    }

    /**
     * Save the fares
     * @param $pirep
     * @param Request $request
     * @throws \Exception
     */
    protected function updateFares($pirep, Request $request)
    {
        if(!$request->filled('fares')) {
            return;
        }

        $fares = [];
        foreach($request->post('fares') as $fare) {
            $fares[] = [
                'fare_id' => $fare['id'],
                'count' => $fare['count'],
            ];
        }

        $this->fareSvc->saveForPirep($pirep, $fares);
    }

    /**
     * Create a new PIREP and place it in a "inprogress" and "prefile" state
     * Once ACARS updates are being processed, then it can go into an 'ENROUTE'
     * status, and whatever other statuses may be defined
     *
     * @param PrefileRequest $request
     * @return PirepResource
     * @throws \App\Exceptions\PirepCancelled
     * @throws \App\Exceptions\AircraftPermissionDenied
     * @throws \Exception
     */
    public function prefile(PrefileRequest $request)
    {
        Log::info('PIREP Prefile, user '.Auth::id(), $request->post());

        $user = Auth::user();

        $attrs = $request->post();
        $attrs['user_id'] = $user->id;
        $attrs['source'] = PirepSource::ACARS;
        $attrs['state'] = PirepState::IN_PROGRESS;
        $attrs['status'] = PirepStatus::PREFILE;

        $pirep = new Pirep($attrs);

        # See if this user is allowed to fly this aircraft
        if(setting('pireps.restrict_aircraft_to_rank', false)) {
            $can_use_ac = $this->userSvc->aircraftAllowed($user, $pirep->aircraft_id);
            if (!$can_use_ac) {
                throw new AircraftPermissionDenied();
            }
        }

        # Find if there's a duplicate, if so, let's work on that
        $dupe_pirep = $this->pirepSvc->findDuplicate($pirep);
        if($dupe_pirep !== false) {
            $pirep = $dupe_pirep;
            $this->checkCancelled($pirep);
        }

        $pirep->save();

        Log::info('PIREP PREFILED');
        Log::info($pirep->id);

        $this->updateFields($pirep, $request);
        $this->updateFares($pirep, $request);

        return new PirepResource($pirep);
    }

    /**
     * Create a new PIREP and place it in a "inprogress" and "prefile" state
     * Once ACARS updates are being processed, then it can go into an 'ENROUTE'
     * status, and whatever other statuses may be defined
     *
     * @param $id
     * @param UpdateRequest $request
     * @return PirepResource
     * @throws \App\Exceptions\PirepCancelled
     * @throws \App\Exceptions\AircraftPermissionDenied
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Exception
     */
    public function update($id, UpdateRequest $request)
    {
        Log::info('PIREP Update, user ' . Auth::id(), $request->post());

        $user = Auth::user();
        $pirep = $this->pirepRepo->find($id);
        $this->checkCancelled($pirep);

        $attrs = $request->post();
        $attrs['user_id'] = Auth::id();

        # If aircraft is being changed, see if this user is allowed to fly this aircraft
        if (array_key_exists('aircraft_id', $attrs)
            && setting('pireps.restrict_aircraft_to_rank', false)
        ) {
            $can_use_ac = $this->userSvc->aircraftAllowed($user, $pirep->aircraft_id);
            if (!$can_use_ac) {
                throw new AircraftPermissionDenied();
            }
        }

        $pirep = $this->pirepRepo->update($attrs, $id);
        $this->updateFields($pirep, $request);
        $this->updateFares($pirep, $request);

        return new PirepResource($pirep);
    }

    /**
     * File the PIREP
     * @param $id
     * @param FileRequest $request
     * @return PirepResource
     * @throws \App\Exceptions\PirepCancelled
     * @throws \App\Exceptions\AircraftPermissionDenied
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function file($id, FileRequest $request)
    {
        Log::info('PIREP file, user ' . Auth::id(), $request->post());

        $user = Auth::user();

        # Check if the status is cancelled...
        $pirep = $this->pirepRepo->find($id);
        $this->checkCancelled($pirep);

        $attrs = $request->post();

        # If aircraft is being changed, see if this user is allowed to fly this aircraft
        if (array_key_exists('aircraft_id', $attrs)
            && setting('pireps.restrict_aircraft_to_rank', false)
        ) {
            $can_use_ac = $this->userSvc->aircraftAllowed($user, $pirep->aircraft_id);
            if (!$can_use_ac) {
                throw new AircraftPermissionDenied();
            }
        }

        $attrs['state'] = PirepState::PENDING;
        $attrs['status'] = PirepStatus::ARRIVED;

        try {
            $pirep = $this->pirepRepo->update($attrs, $id);
            $pirep = $this->pirepSvc->create($pirep);
            $this->updateFields($pirep, $request);
            $this->updateFares($pirep, $request);
        } catch (\Exception $e) {
            Log::error($e);
        }

        # See if there there is any route data posted
        # If there isn't, then just write the route data from the
        # route that's been posted from the PIREP
        $w = ['pirep_id' => $pirep->id, 'type' => AcarsType::ROUTE];
        $count = Acars::where($w)->count(['id']);
        if($count === 0) {
            $this->pirepSvc->saveRoute($pirep);
        }

        return new PirepResource($pirep);
    }

    /**
     * Cancel the PIREP
     * @param $id
     * @param Request $request
     * @return PirepResource
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function cancel($id, Request $request)
    {
        Log::info('PIREP Cancel, user ' . Auth::id(), $request->post());

        $pirep = $this->pirepRepo->update([
            'state' => PirepState::CANCELLED,
        ], $id);

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
     * Return the routes for the ACARS line
     * @param $id
     * @param Request $request
     * @return AcarsRouteResource
     */
    public function acars_get($id, Request $request)
    {
        $this->pirepRepo->find($id);

        return new AcarsRouteResource(Acars::where([
            'pirep_id' => $id,
            'type' => AcarsType::FLIGHT_PATH
        ])->orderBy('created_at', 'asc')->get());
    }

    /**
     * Post ACARS updates for a PIREP
     * @param $id
     * @param PositionRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\PirepCancelled
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function acars_store($id, PositionRequest $request)
    {
        # Check if the status is cancelled...
        $pirep = $this->pirepRepo->find($id);
        $this->checkCancelled($pirep);

        Log::debug(
            'Posting ACARS update (user: '.Auth::user()->pilot_id.', pirep id :'.$id.'): ',
            $request->post()
        );

        $count = 0;
        $positions = $request->post('positions');
        foreach($positions as $position)
        {
            $position['pirep_id'] = $id;
            $position['type'] = AcarsType::FLIGHT_PATH;

            $update = Acars::create($position);
            $update->save();

            ++$count;
        }

        # Change the PIREP status
        $pirep->status = PirepStatus::ENROUTE;
        $pirep->save();

        return $this->message($count . ' positions added', $count);
    }

    /**
     * Post ACARS LOG update for a PIREP. These updates won't show up on the map
     * But rather in a log file.
     * @param $id
     * @param LogRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\PirepCancelled
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function acars_logs($id, LogRequest $request)
    {
        # Check if the status is cancelled...
        $pirep = $this->pirepRepo->find($id);
        $this->checkCancelled($pirep);

        Log::debug('Posting ACARS log, PIREP: '.$id, $request->post());

        $count = 0;
        $logs = $request->post('logs');
        foreach($logs as $log) {

            $log['pirep_id'] = $id;
            $log['type'] = AcarsType::LOG;

            $acars = Acars::create($log);
            $acars->save();
            ++$count;
        }

        return $this->message($count . ' logs added', $count);
    }

    /**
     * Post ACARS LOG update for a PIREP. These updates won't show up on the map
     * But rather in a log file.
     * @param $id
     * @param EventRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\PirepCancelled
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function acars_events($id, EventRequest $request)
    {
        # Check if the status is cancelled...
        $pirep = $this->pirepRepo->find($id);
        $this->checkCancelled($pirep);

        Log::debug('Posting ACARS event, PIREP: ' . $id, $request->post());

        $count = 0;
        $logs = $request->post('events');
        foreach ($logs as $log) {

            $log['pirep_id'] = $id;
            $log['type'] = AcarsType::LOG;
            $log['log'] = $log['event'];

            $acars = Acars::create($log);
            $acars->save();
            ++$count;
        }

        return $this->message($count . ' logs added', $count);
    }

    /**
     * Add a new comment
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function comments_get($id, Request $request)
    {
        $pirep = $this->pirepRepo->find($id);
        return PirepCommentResource::collection($pirep->comments);
    }

    /**
     * Add a new comment
     * @param $id
     * @param CommentRequest $request
     * @return PirepCommentResource
     * @throws \App\Exceptions\PirepCancelled
     */
    public function comments_post($id, CommentRequest $request)
    {
        $pirep = $this->pirepRepo->find($id);
        $this->checkCancelled($pirep);

        Log::debug('Posting comment, PIREP: '.$id, $request->post());

        # Add it
        $comment = new PirepComment($request->post());
        $comment->pirep_id = $id;
        $comment->user_id = Auth::id();
        $comment->save();

        return new PirepCommentResource($comment);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     */
    public function finances_get($id, Request $request)
    {
        $pirep = $this->pirepRepo->find($id);
        $transactions = $this->journalRepo->getAllForObject($pirep);
        return JournalTransactionResource::collection($transactions);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function finances_recalculate($id, Request $request)
    {
        $pirep = $this->pirepRepo->find($id);
        $this->financeSvc->processFinancesForPirep($pirep);

        $pirep->refresh();
        $transactions = $this->journalRepo->getAllForObject($pirep);
        return JournalTransactionResource::collection($transactions['transactions']);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function route_get($id, Request $request)
    {
        $this->pirepRepo->find($id);

        return AcarsRouteResource::collection(Acars::where([
            'pirep_id' => $id,
            'type' => AcarsType::ROUTE
        ])->orderBy('order', 'asc')->get());
    }

    /**
     * Post the ROUTE for a PIREP, can be done from the ACARS log
     * @param $id
     * @param RouteRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\PirepCancelled
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function route_post($id, RouteRequest $request)
    {
        # Check if the status is cancelled...
        $pirep = $this->pirepRepo->find($id);
        $this->checkCancelled($pirep);

        Log::info('Posting ROUTE, PIREP: '.$id, $request->post());

        $count = 0;
        $route = $request->post('route', []);
        foreach($route as $position) {
            $position['pirep_id'] = $id;
            $position['type'] = AcarsType::ROUTE;

            $acars = Acars::create($position);
            $acars->save();

            ++$count;
        }

        return $this->message($count . ' points added', $count);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
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
