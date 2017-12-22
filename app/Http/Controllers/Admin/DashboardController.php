<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Repositories\PirepRepository;
use App\Repositories\UserRepository;

class DashboardController extends BaseController
{
    private $pirepRepo, $userRepo;

    public function __construct(
        PirepRepository $pirepRepo,
        UserRepository $userRepo
    ) {
        $this->pirepRepo = $pirepRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * Display a listing of the Airlines.
     */
    public function index(Request $request)
    {
        /*Feed::$cacheDir = storage_path('app');
        Feed::$cacheExpire = '5 hours';

        $feed = Feed::loadRss(config('phpvms.news_feed_url'));*/
        $feed = [];
        return view('admin.dashboard.index', [
            'feed' => $feed,
            'pending_pireps' => $this->pirepRepo->getPendingCount(),
            'pending_users' => $this->userRepo->getPendingCount(),
        ]);
    }
}
