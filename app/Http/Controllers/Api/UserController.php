<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\AppBaseController;
use App\Repositories\UserRepository;

use App\Models\UserBid;

use App\Http\Resources\Flight as FlightResource;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\UserBid as UserBidResource;


class UserController extends AppBaseController
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Return the profile for the currently auth'd user
     */
    public function index(Request $request)
    {
        UserResource::withoutWrapping();
        return new UserResource($request->user);
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
     */
    public function bids($id)
    {
        $flights = UserBid::where(['user_id' => $id])->get()
            ->pluck('flight');

        return FlightResource::collection($flights);
    }

}
