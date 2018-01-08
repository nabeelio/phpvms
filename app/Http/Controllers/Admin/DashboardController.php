<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Illuminate\Http\Request;

use App\Repositories\NewsRepository;
use App\Repositories\PirepRepository;
use App\Repositories\UserRepository;

class DashboardController extends BaseController
{
    private $newsRepo, $pirepRepo, $userRepo;

    public function __construct(
        NewsRepository $newsRepo,
        PirepRepository $pirepRepo,
        UserRepository $userRepo
    ) {
        $this->newsRepo = $newsRepo;
        $this->pirepRepo = $pirepRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * Display a listing of the Airlines.
     */
    public function index(Request $request)
    {
        return view('admin.dashboard.index', [
            'news' => $this->newsRepo->getLatest(),
            'pending_pireps' => $this->pirepRepo->getPendingCount(),
            'pending_users' => $this->userRepo->getPendingCount(),
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function news(Request $request)
    {
        if($request->isMethod('post')) {
            $attrs = $request->post();
            $attrs['user_id'] = Auth::user()->id;

            $this->newsRepo->create($request->post());
        } elseif ($request->isMethod('delete')) {
            $news_id = $request->input('news_id');
            $this->newsRepo->delete($news_id);
        }

        return view('admin.dashboard.news', [
            'news' => $this->newsRepo->getLatest(),
        ]);
    }
}
