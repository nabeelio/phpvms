<?php

namespace App\Http\Controllers\Frontend;

use App\Interfaces\Controller;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

/**
 * Class UserController
 * @package App\Http\Controllers\Frontend
 */
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
        return view('users.index', [
            'country' => new \League\ISO3166\ISO3166(),
            'users'   => $this->userRepo
                ->with(['airline', 'current_airport'])
                ->orderBy('name', 'desc')
                ->paginate(),
        ]);
    }
}
