<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Controller;
use App\Http\Resources\News as NewsResource;
use App\Repositories\NewsRepository;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    private NewsRepository $newsRepo;

    /**
     * AirlineController constructor.
     *
     * @param NewsRepository $newsRepo
     */
    public function __construct(NewsRepository $newsRepo)
    {
        $this->newsRepo = $newsRepo;
    }

    /**
     * Return all the airlines, paginated
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $news = $this->newsRepo
            ->orderBy('created_at', 'desc')
            ->paginate();

        return NewsResource::collection($news);
    }
}
