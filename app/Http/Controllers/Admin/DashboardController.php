<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Feed;


class DashboardController extends BaseController
{
    /**
     * Display a listing of the Airlines.
     */
    public function index(Request $request)
    {
        Feed::$cacheDir = storage_path();
        Feed::$cacheExpire = '5 hours';

        $feed = Feed::loadRss(config('phpvms.feed_url'));
        return view('admin.dashboard', [
            'feed' => $feed,
        ]);
    }
}
