<?php

namespace App\Http\Controllers\Api;

use App\Repositories\SubfleetRepository;
use App\Services\UserService;
use Auth;
use Illuminate\Http\Request;

use App\Repositories\UserRepository;

use App\Models\UserBid;

use App\Http\Resources\Subfleet as SubfleetResource;
use App\Http\Resources\Flight as FlightResource;
use App\Http\Resources\User as UserResource;


class UserController extends RestController
{
    protected $subfleetRepo,
              $userRepo,
              $userSvc;

    public function __construct(
        SubfleetRepository $subfleetRepo,
        UserRepository $userRepo,
        UserService $userSvc
    ) {
        $this->subfleetRepo = $subfleetRepo;
        $this->userRepo = $userRepo;
        $this->userSvc = $userSvc;
    }

    /**
     * Return the profile for the currently auth'd user
     */
    public function index(Request $request)
    {
        UserResource::withoutWrapping();
        return new UserResource(Auth::user());
    }

    /**
     * Get the profile for the passed-in user
     */
    public function get($id)
    {
        UserResource::withoutWrapping();
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
        if($request->id === null) {
            $id = Auth::user()->id;
        } else {
            $id = $request->id;
        }

        $user = $this->userRepo->find($id);
        $subfleets = $this->userSvc->getAllowableSubfleets($user);

        SubfleetResource::withoutWrapping();
        return SubfleetResource::collection($subfleets);
    }

}
