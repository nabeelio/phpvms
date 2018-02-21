<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Flight as FlightResource;
use App\Http\Resources\Pirep as PirepResource;
use App\Http\Resources\Subfleet as SubfleetResource;
use App\Http\Resources\User as UserResource;
use App\Models\Enums\PirepState;
use App\Models\UserBid;
use App\Repositories\Criteria\WhereCriteria;
use App\Repositories\PirepRepository;
use App\Repositories\SubfleetRepository;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Auth;
use Illuminate\Http\Request;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;


class UserController extends RestController
{
    protected $pirepRepo,
              $subfleetRepo,
              $userRepo,
              $userSvc;

    /**
     * UserController constructor.
     * @param PirepRepository $pirepRepo
     * @param SubfleetRepository $subfleetRepo
     * @param UserRepository $userRepo
     * @param UserService $userSvc
     */
    public function __construct(
        PirepRepository $pirepRepo,
        SubfleetRepository $subfleetRepo,
        UserRepository $userRepo,
        UserService $userSvc
    ) {
        $this->pirepRepo = $pirepRepo;
        $this->subfleetRepo = $subfleetRepo;
        $this->userRepo = $userRepo;
        $this->userSvc = $userSvc;
    }

    /**
     * @param Request $request
     * @return int|mixed
     */
    protected function getUserId(Request $request)
    {
        if ($request->id === null) {
            return Auth::user()->id;
        }

        return $request->id;
    }

    /**
     * Return the profile for the currently auth'd user
     * @param Request $request
     * @return UserResource
     */
    public function index(Request $request)
    {
        return new UserResource(Auth::user());
    }

    /**
     * Get the profile for the passed-in user
     * @param $id
     * @return UserResource
     */
    public function get($id)
    {
        return new UserResource($this->userRepo->find($id));
    }

    /**
     * Return all of the bids for the passed-in user
     * @param $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function bids($id)
    {
        $flights = UserBid::where(['user_id' => $id])->get()
            ->pluck('flight');

        return FlightResource::collection($flights);
    }

    /**
     * Return the fleet that this user is allowed to
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function fleet(Request $request)
    {
        $user = $this->userRepo->find($this->getUserId($request));
        $subfleets = $this->userSvc->getAllowableSubfleets($user);

        return SubfleetResource::collection($subfleets);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws RepositoryException
     */
    public function pireps(Request $request)
    {
        $this->pirepRepo->pushCriteria(new RequestCriteria($request));

        $where = [
            'user_id' => $this->getUserId($request),
        ];

        if(filled($request->query('state'))) {
            $where['state'] = $request->query('state');
        } else {
            $where[] = ['state', '!=', PirepState::CANCELLED];
        }

        $this->pirepRepo->pushCriteria(new WhereCriteria($request, $where));

        $pireps = $this->pirepRepo
            ->orderBy('created_at', 'desc')
            ->paginate();

        return PirepResource::collection($pireps);
    }
}
