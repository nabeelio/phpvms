<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    /**
     * Display a listing of the Airlines.
     */
    public function index(Request $request)
    {
        /*Feed::$cacheDir = storage_path('app');
        Feed::$cacheExpire = '5 hours';

        $feed = Feed::loadRss(config('phpvms.feed_url'));*/
        $feed = [];
        return view('admin.dashboard', [
            'feed' => $feed,
        ]);
    }
}
