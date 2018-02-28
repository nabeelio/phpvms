<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userRepo;

    /**
     * UserController constructor.
     * @param UserRepository $userRepo
     */
    public function __construct(
        UserRepository $userRepo
    ) {
        $this->userRepo = $userRepo;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return $this->view('users.index',[
            'country' => new \League\ISO3166\ISO3166(),
            'users' => $this->userRepo->orderBy('name', 'desc')->paginate(),
        ]);
    }
}
