<?php

namespace App\Http\Controllers\Frontend;

use App\Contracts\Controller;
use App\Models\Enums\UserState;
use App\Repositories\Criteria\WhereCriteria;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Log;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class UserController
 */
class UserController extends Controller
{
    private $userRepo;

    /**
     * UserController constructor.
     *
     * @param UserRepository $userRepo
     */
    public function __construct(
        UserRepository $userRepo
    ) {
        $this->userRepo = $userRepo;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $where = [];

        if (setting('pilots.hide_inactive')) {
            $where['state'] = UserState::ACTIVE;
        }

        try {
            $this->userRepo->pushCriteria(new WhereCriteria($request, $where));
        } catch (RepositoryException $e) {
            Log::emergency($e);
        }

        $users = $this->userRepo
            ->with(['airline', 'current_airport'])
            ->orderBy('name', 'desc')
            ->paginate();

        return view('users.index', [
            'country' => new \League\ISO3166\ISO3166(),
            'users'   => $users,
        ]);
    }
}
