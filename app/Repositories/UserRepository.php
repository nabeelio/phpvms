<?php
namespace App\Repositories;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Enums\UserState;
use App\Repositories\Criteria\WhereCriteria;

class UserRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name' => 'like',
        'email' => 'like',
        'home_airport_id',
        'curr_airport_id',
        'state'
    ];

    public function model()
    {
        return User::class;
    }

    /**
     * Number of PIREPs that are pending
     * @return mixed
     */
    public function getPendingCount()
    {
        $where = [
            'state' => UserState::PENDING,
        ];

        $users = $this->orderBy('created_at', 'desc')->findWhere($where)->count();
        return $users;
    }

    /**
     * Create the search criteria and return this with the stuff pushed
     * @param Request $request
     * @param bool $only_active
     * @return $this
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function searchCriteria(Request $request, bool $only_active = true)
    {
        $where = [];

        if($only_active) {
            $where['state'] = UserState::ACTIVE;
        }

        if ($request->filled('name')) {
            $where['name'] = $request->name;
        }

        if ($request->filled('email')) {
            $where['email'] = $request->email;
        }

        if ($request->filled('state')) {
            $where['state'] = $request->state;
        }

        $this->pushCriteria(new WhereCriteria($request, $where));
        return $this;
    }
}
