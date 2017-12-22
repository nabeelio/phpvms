<?php
namespace App\Repositories;

use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CacheableInterface;

use App\Models\User;
use App\Models\Enums\PilotState;
use App\Repositories\Criteria\WhereCriteria;
use App\Repositories\Traits\CacheableRepository;

class UserRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

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
            'state' => PilotState::PENDING,
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
            $where['state'] = PilotState::ACTIVE;
        }

        if ($request->filled('name')) {
            $where['name'] = $request->name;
        }

        if ($request->filled('email')) {
            $where['email'] = $request->email;
        }

        $this->pushCriteria(new WhereCriteria($request, $where));
        return $this;
    }
}
