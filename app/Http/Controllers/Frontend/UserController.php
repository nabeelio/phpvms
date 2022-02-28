<?php

namespace App\Http\Controllers\Frontend;

use App\Contracts\Controller;
use App\Models\Enums\UserState;
use App\Repositories\Criteria\WhereCriteria;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use League\ISO3166\ISO3166;
use Prettus\Repository\Exceptions\RepositoryException;

class UserController extends Controller
{
    private UserRepository $userRepo;

    /**
     * @param UserRepository $userRepo
     */
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $with = ['airline', 'current_airport', 'fields.field', 'home_airport', 'rank'];
        $with_count = ['awards'];

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
            ->withCount($with_count)
            ->with($with)
            ->orderBy('pilot_id', 'asc')
            ->paginate();

        return view('users.index', [
            'country' => new ISO3166(),
            'users'   => $users,
        ]);
    }
}
