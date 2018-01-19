<?php

namespace App\Http\Controllers\Frontend;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    private $userRepo;

    public function __construct(
        UserRepository $userRepo
    ) {
        $this->userRepo = $userRepo;
    }

    /**
     *
     */
    public function index(Request $request)
    {
        return $this->view('users.index',[
            'country' => new \League\ISO3166\ISO3166(),
            'users' => $this->userRepo->orderBy('name', 'desc')->paginate(25),
        ]);
    }
}
