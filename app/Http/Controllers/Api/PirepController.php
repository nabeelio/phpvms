<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Acars\UpdateRequest;
use Auth;
use Log;
use Illuminate\Http\Request;

use App\Http\Requests\Acars\CommentRequest;
use App\Http\Requests\Acars\FileRequest;
use App\Http\Requests\Acars\LogRequest;
use App\Http\Requests\Acars\PositionRequest;
use App\Http\Requests\Acars\PrefileRequest;
use App\Http\Requests\Acars\RouteRequest;

use App\Models\Acars;
use App\Models\Pirep;
use App\Models\PirepComment;

use App\Models\Enums\AcarsType;
use App\Models\Enums\PirepState;
use App\Models\Enums\PirepStatus;

use App\Services\GeoService;
use App\Services\PIREPService;
use App\Repositories\AcarsRepository;
use App\Repositories\PirepRepository;

use App\Http\Resources\Pirep as PirepResource;
use App\Http\Resources\PirepComment as PirepCommentResource;
use App\Http\Resources\AcarsRoute as AcarsRouteResource;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PirepController extends RestController
{
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

    /**
     * Check if a PIREP is cancelled
     * @param $pirep
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    protected function checkCancelled(Pirep $pirep)
    {
        if (!$pirep->allowedUpdates()) {
            throw new BadRequestHttpException('PIREP has been cancelled, comments can\'t be posted');
        }
    }

    /**
     * @param $id
     * @return PirepResource
     */
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
     * @param PrefileRequest $request
     * @return PirepResource
     */
    public function prefile(PrefileRequest $request)
    {
        Log::info('PIREP Prefile, user '.Auth::id(), $request->post());

        $attrs = $request->post();
        $attrs['user_id'] = Auth::id();
        $attrs['state'] = PirepState::IN_PROGRESS;
        $attrs['status'] = PirepStatus::PREFILE;

        $pirep = new Pirep($attrs);

        # Find if there's a duplicate, if so, let's work on that
        $dupe_pirep = $this->pirepSvc->findDuplicate($pirep);
        if($dupe_pirep !== false) {
            $pirep = $dupe_pirep;
        }

        $pirep->save();

        Log::info('PIREP PREFILED');
        Log::info($pirep->id);

        PirepResource::withoutWrapping();
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
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update($id, UpdateRequest $request)
    {
        Log::info('PIREP Update, user ' . Auth::id(), $request->post());

        $pirep = $this->pirepRepo->find($id);
        $this->checkCancelled($pirep);

        $attrs = $request->post();
        $attrs['user_id'] = Auth::id();

        $pirep = $this->pirepRepo->update($attrs, $id);

        PirepResource::withoutWrapping();
        return new PirepResource($pirep);
    }

    /**
     * File the PIREP
     * @param $id
     * @param FileRequest $request
     * @return PirepResource
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function file($id, FileRequest $request)
    {
        Log::info('PIREP file, user ' . Auth::id(), $request->post());

        # Check if the status is cancelled...
        $pirep = $this->pirepRepo->find($id);
        $this->checkCancelled($pirep);

        $attrs = $request->post();
        $attrs['state'] = PirepState::PENDING;
        $attrs['status'] = PirepStatus::ARRIVED;

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
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function cancel($id, Request $request)
    {
        Log::info('PIREP Cancel, user ' . Auth::id(), $request->post());

        $pirep = $this->pirepRepo->update([
            'state' => PirepState::CANCELLED,
        ], $id);

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
     * @param PositionRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function acars_store($id, PositionRequest $request)
    {
        # Check if the status is cancelled...
        $pirep = $this->pirepRepo->find($id);
        $this->checkCancelled($pirep);

        Log::info(
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
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function acars_log($id, LogRequest $request)
    {
        # Check if the status is cancelled...
        $pirep = $this->pirepRepo->find($id);
        $this->checkCancelled($pirep);

        Log::info('Posting ACARS log, PIREP: '.$id, $request->post());

        $count = 0;
        $logs = $request->post('logs');
        foreach($logs as $log) {

            $log['pirep_id'] = $id;
            $log['type'] = AcarsType::LOG;

            if(array_has($log, 'event')) {
                $log['log'] = $log['event'];
            }

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
     */
    public function comments_post($id, CommentRequest $request)
    {
        $pirep = $this->pirepRepo->find($id);
        $this->checkCancelled($pirep);

        Log::info('Posting comment, PIREP: '.$id, $request->post());

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
     * @param RouteRequest $request
     * @return AcarsRouteResource
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function route_post($id, RouteRequest $request)
    {
        # Check if the status is cancelled...
        $pirep = $this->pirepRepo->find($id);
        $this->checkCancelled($pirep);

        Log::info('Posting ROUTE, PIREP: '.$id, $request->post());

        $route = $request->post('route', []);
        foreach($route as $position) {
            $position['pirep_id'] = $id;
            $position['type'] = AcarsType::ROUTE;

            $acars = Acars::create($position);
            $acars->save();
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
