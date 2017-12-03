<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\PirepRepository;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    private $pirepRepo;

    public function __construct(
        PirepRepository $pirepRepo
    ) {
        $this->pirepRepo = $pirepRepo;
    }

    /**
     * Display a listing of the Airlines.
     */
    public function index(Request $request)
    {
        /*Feed::$cacheDir = storage_path('app');
        Feed::$cacheExpire = '5 hours';

        $feed = Feed::loadRss(config('phpvms.feed_url'));*/
        $feed = [];
        return view('admin.dashboard.index', [
            'feed' => $feed,
            'pending_pireps' => $this->pirepRepo->getPendingCount(),
        ]);
    }
}
