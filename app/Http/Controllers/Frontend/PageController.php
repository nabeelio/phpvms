<?php

namespace App\Http\Controllers\Frontend;

use App\Contracts\Controller;
use App\Exceptions\PageNotFound;
use App\Repositories\PageRepository;
use Exception;

class PageController extends Controller
{
    private $pageRepo;

    /**
     * @param \App\Repositories\PageRepository $pageRepo
     */
    public function __construct(PageRepository $pageRepo)
    {
        $this->pageRepo = $pageRepo;
    }

    /**
     * Show the page
     *
     * @param $slug
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($slug)
    {
        $page = $this->pageRepo->findWhere(['slug' => $slug])->first();
        if (!$page) {
            throw new PageNotFound(new Exception('Page not found'));
        }

        return view('pages.index', ['page' => $page]);
    }
}
