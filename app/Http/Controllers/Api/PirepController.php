<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Controller;
use App\Events\PirepPrefiled;
use App\Events\PirepUpdated;
use App\Exceptions\AircraftPermissionDenied;
use App\Exceptions\PirepCancelled;
use App\Http\Requests\Acars\CommentRequest;
use App\Http\Requests\Acars\FieldsRequest;
use App\Http\Requests\Acars\FileRequest;
use App\Http\Requests\Acars\PrefileRequest;
use App\Http\Requests\Acars\RouteRequest;
use App\Http\Requests\Acars\UpdateRequest;
use App\Http\Resources\AcarsRoute as AcarsRouteResource;
use App\Http\Resources\JournalTransaction as JournalTransactionResource;
use App\Http\Resources\Pirep as PirepResource;
use App\Http\Resources\PirepComment as PirepCommentResource;
use App\Http\Resources\PirepFieldCollection;
use App\Models\Acars;
use App\Models\Enums\AcarsType;
use App\Models\Enums\PirepFieldSource;
use App\Models\Enums\PirepSource;
use App\Models\Enums\PirepState;
use App\Models\Enums\PirepStatus;
use App\Models\Pirep;
use App\Models\PirepComment;
use App\Models\SimBrief;
use App\Repositories\AcarsRepository;
use App\Repositories\JournalRepository;
use App\Repositories\PirepRepository;
use App\Services\FareService;
use App\Services\Finance\PirepFinanceService;
use App\Services\PirepService;
use App\Services\SimBriefService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PirepController extends Controller
{
    private $acarsRepo;
    private $fareSvc;
    private $financeSvc;
    private $journalRepo;
    private $pirepRepo;
    private $pirepSvc;
    private $userSvc;

    /**
     * @param AcarsRepository     $acarsRepo
     * @param FareService         $fareSvc
     * @param PirepFinanceService $financeSvc
     * @param JournalRepository   $journalRepo
     * @param PirepRepository     $pirepRepo
     * @param PirepService        $pirepSvc
     * @param UserService         $userSvc
     */
    public function __construct(
        AcarsRepository $acarsRepo,
        FareService $fareSvc,
        PirepFinanceService $financeSvc,
        JournalRepository $journalRepo,
        PirepRepository $pirepRepo,
        PirepService $pirepSvc,
        UserService $userSvc
    ) {
        $this->acarsRepo = $acarsRepo;
        $this->fareSvc = $fareSvc;
        $this->financeSvc = $financeSvc;
        $this->journalRepo = $journalRepo;
        $this->pirepRepo = $pirepRepo;
        $this->pirepSvc = $pirepSvc;
        $this->userSvc = $userSvc;
    }

    /**
     * Parse any PIREP added in
     *
     * @param Request $request
     *
     * @return array|null|string
     */
    protected function parsePirep(Request $request)
    {
        $attrs = $request->input();

        if (array_key_exists('created_at', $attrs)) {
            $attrs['created_at'] = Carbon::createFromTimeString($attrs['created_at']);
        }

        if (array_key_exists('submitted_at', $attrs)) {
            $attrs['submitted_at'] = Carbon::createFromTimeString($attrs['submitted_at']);
        }

        if (array_key_exists('updated_at', $attrs)) {
            $attrs['updated_at'] = Carbon::createFromTimeString($attrs['updated_at']);
        }

        return $attrs;
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
     * @param         $pirep
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
                'name'   => $field_name,
                'value'  => $field_value,
                'source' => PirepFieldSource::ACARS,
            ];
        }

        $this->pirepSvc->updateCustomFields($pirep->id, $pirep_fields);
    }

    /**
     * Save the fares
     *
     * @param         $pirep
     * @param Request $request
     *
     * @throws \Exception
     */
    protected function updateFares($pirep, Request $request)
    {
        if (!$request->filled('fares')) {
            return;
        }

        $fares = [];
        foreach ($request->post('fares') as $fare) {
            $fares[] = [
                'fare_id' => $fare['id'],
                'count'   => $fare['count'],
            ];
        }

        $this->fareSvc->saveForPirep($pirep, $fares);
    }

    /**
     * @param string $id The PIREP ID
     *
     * @return PirepResource
     */
    public function get($id)
    {
        $with = [
            'acars',
            'arr_airport',
            'dpt_airport',
            'comments',
            'flight',
            'simbrief',
            'user',
        ];

        $pirep = $this->pirepRepo
            ->with($with)
            ->find($id);

        return new PirepResource($pirep);
    }

    /**
     * Create a new PIREP and place it in a "inprogress" and "prefile" state
     * Once ACARS updates are being processed, then it can go into an 'ENROUTE'
     * status, and whatever other statuses may be defined
     *
     * @param PrefileRequest $request
     *
     * @throws \App\Exceptions\AircraftNotAtAirport
     * @throws \App\Exceptions\UserNotAtAirport
     * @throws \App\Exceptions\PirepCancelled
     * @throws \App\Exceptions\AircraftPermissionDenied
     * @throws \Exception
     *
     * @return PirepResource
     */
    public function prefile(PrefileRequest $request)
    {
        Log::info('PIREP Prefile, user '.Auth::id(), $request->post());

        /**
         * @var $user \App\Models\User
         */
        $user = Auth::user();

        $attrs = $this->parsePirep($request);
        $attrs['source'] = PirepSource::ACARS;

        $pirep = $this->pirepSvc->prefile($user, $attrs);

        Log::info('PIREP PREFILED');
        Log::info($pirep->id);

        $this->updateFields($pirep, $request);
        $this->updateFares($pirep, $request);

        event(new PirepPrefiled($pirep));

        return $this->get($pirep->id);
    }

    /**
     * Create a new PIREP and place it in a "inprogress" and "prefile" state
     * Once ACARS updates are being processed, then it can go into an 'ENROUTE'
     * status, and whatever other statuses may be defined
     *
     * @param               $pirep_id
     * @param UpdateRequest $request
     *
     * @throws \App\Exceptions\PirepCancelled
     * @throws \App\Exceptions\AircraftPermissionDenied
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     * @throws \Exception
     *
     * @return PirepResource
     */
    public function update($pirep_id, UpdateRequest $request)
    {
        Log::info('PIREP Update, user '.Auth::id());
        Log::info($request->getContent());

        $user = Auth::user();
        $pirep = Pirep::find($pirep_id);
        $this->checkCancelled($pirep);

        $attrs = $this->parsePirep($request);
        $attrs['user_id'] = Auth::id();

        // If aircraft is being changed, see if this user is allowed to fly this aircraft
        if (array_key_exists('aircraft_id', $attrs)
            && setting('pireps.restrict_aircraft_to_rank', false)
        ) {
            $can_use_ac = $this->userSvc->aircraftAllowed($user, $pirep->aircraft_id);
            if (!$can_use_ac) {
                throw new AircraftPermissionDenied($user, $pirep->aircraft);
            }
        }

        $pirep = $this->pirepRepo->update($attrs, $pirep_id);
        $this->updateFields($pirep, $request);
        $this->updateFares($pirep, $request);

        event(new PirepUpdated($pirep));

        return $this->get($pirep->id);
    }

    /**
     * File the PIREP
     *
     * @param             $pirep_id
     * @param FileRequest $request
     *
     * @throws \App\Exceptions\PirepCancelled
     * @throws \App\Exceptions\AircraftPermissionDenied
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     *
     * @return PirepResource
     */
    public function file($pirep_id, FileRequest $request)
    {
        Log::info('PIREP file, user '.Auth::id(), $request->post());

        $user = Auth::user();

        // Check if the status is cancelled...
        $pirep = Pirep::find($pirep_id);
        $this->checkCancelled($pirep);

        $attrs = $this->parsePirep($request);

        // If aircraft is being changed, see if this user is allowed to fly this aircraft
        if (array_key_exists('aircraft_id', $attrs)
            && setting('pireps.restrict_aircraft_to_rank', false)
        ) {
            $can_use_ac = $this->userSvc->aircraftAllowed($user, $pirep->aircraft_id);
            if (!$can_use_ac) {
                throw new AircraftPermissionDenied($user, $pirep->aircraft);
            }
        }

        try {
            $pirep = $this->pirepSvc->file($pirep, $attrs);
            $this->updateFields($pirep, $request);
            $this->updateFares($pirep, $request);
        } catch (\Exception $e) {
            Log::error($e);
        }

        // See if there there is any route data posted
        // If there isn't, then just write the route data from the
        // route that's been posted from the PIREP
        $w = ['pirep_id' => $pirep->id, 'type' => AcarsType::ROUTE];
        $count = Acars::where($w)->count(['id']);
        if ($count === 0) {
            $this->pirepSvc->saveRoute($pirep);
        }

        $this->pirepSvc->submit($pirep);

        return $this->get($pirep->id);
    }

    /**
     * Cancel the PIREP
     *
     * @param         $pirep_id
     * @param Request $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return PirepResource
     */
    public function cancel($pirep_id, Request $request)
    {
        Log::info('PIREP Cancel, user '.Auth::id(), $request->post());

        $pirep = Pirep::find($pirep_id);
        $this->pirepSvc->cancel($pirep);

        return new PirepResource($pirep);
    }

    /**
     * Add a new comment
     *
     * @param $id
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function comments_get($id)
    {
        $pirep = Pirep::find($id);
        return PirepCommentResource::collection($pirep->comments);
    }

    /**
     * Add a new comment
     *
     * @param                $id
     * @param CommentRequest $request
     *
     * @throws \App\Exceptions\PirepCancelled
     *
     * @return PirepCommentResource
     */
    public function comments_post($id, CommentRequest $request)
    {
        $pirep = Pirep::find($id);
        $this->checkCancelled($pirep);

        Log::debug('Posting comment, PIREP: '.$id, $request->post());

        // Add it
        $comment = new PirepComment($request->post());
        $comment->pirep_id = $id;
        $comment->user_id = Auth::id();
        $comment->save();

        return new PirepCommentResource($comment);
    }

    /**
     * Get all of the fields for a PIREP
     *
     * @param $pirep_id
     *
     * @return PirepFieldCollection
     */
    public function fields_get($pirep_id)
    {
        $pirep = Pirep::find($pirep_id);
        return new PirepFieldCollection($pirep->fields);
    }

    /**
     * Set any fields for a PIREP
     *
     * @param string        $pirep_id
     * @param FieldsRequest $request
     *
     * @return PirepFieldCollection
     */
    public function fields_post($pirep_id, FieldsRequest $request)
    {
        $pirep = Pirep::find($pirep_id);
        $this->checkCancelled($pirep);

        $this->updateFields($pirep, $request);

        return new PirepFieldCollection($pirep->fields);
    }

    /**
     * @param $id
     *
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function finances_get($id)
    {
        $pirep = Pirep::find($id);
        $transactions = $this->journalRepo->getAllForObject($pirep);
        return JournalTransactionResource::collection($transactions);
    }

    /**
     * @param         $id
     * @param Request $request
     *
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function finances_recalculate($id, Request $request)
    {
        $pirep = Pirep::find($id);
        $this->financeSvc->processFinancesForPirep($pirep);

        $pirep->refresh();

        $transactions = $this->journalRepo->getAllForObject($pirep);
        return JournalTransactionResource::collection($transactions['transactions']);
    }

    /**
     * @param         $id
     * @param Request $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function route_get($id, Request $request)
    {
        $pirep = Pirep::find($id);

        return AcarsRouteResource::collection(Acars::with('pirep')->where([
            'pirep_id' => $id,
            'type'     => AcarsType::ROUTE,
        ])->orderBy('order', 'asc')->get());
    }

    /**
     * Post the ROUTE for a PIREP, can be done from the ACARS log
     *
     * @param              $id
     * @param RouteRequest $request
     *
     * @throws \App\Exceptions\PirepCancelled
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \Exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function route_post($id, RouteRequest $request)
    {
        // Check if the status is cancelled...
        $pirep = Pirep::find($id);
        $this->checkCancelled($pirep);

        Log::info('Posting ROUTE, PIREP: '.$id, $request->post());

        // Delete the route before posting a new one
        Acars::where([
            'pirep_id' => $id,
            'type'     => AcarsType::ROUTE,
        ])->delete();

        $count = 0;
        $route = $request->post('route', []);
        if (\count($route) === 0) {
            return $this->message('No points to add');
        }

        foreach ($route as $position) {
            $position['pirep_id'] = $id;
            $position['type'] = AcarsType::ROUTE;

            if (isset($position['id'])) {
                Acars::updateOrInsert(['id' => $position['id']], $position);
            } else {
                $acars = Acars::create($position);
                $acars->save();
            }

            $count++;
        }

        return $this->message($count.' points added', $count);
    }

    /**
     * @param         $id
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function route_delete($id, Request $request)
    {
        $pirep = Pirep::find($id);

        Acars::where([
            'pirep_id' => $id,
            'type'     => AcarsType::ROUTE,
        ])->delete();

        return $this->message('Route deleted');
    }
}
