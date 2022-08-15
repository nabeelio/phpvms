<?php

namespace App\Http\Composers;

use App\Contracts\Composer;
use App\Repositories\PageRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PageLinksComposer extends Composer
{
    private static $fields = ['id', 'name', 'slug', 'icon', 'type', 'link', 'new_window'];

    /** @var \App\Repositories\PageRepository */
    private PageRepository $pageRepo;

    /**
     * PageLinksComposer constructor.
     *
     * @param \App\Repositories\PageRepository $pageRepo
     */
    public function __construct(PageRepository $pageRepo)
    {
        $this->pageRepo = $pageRepo;
    }

    /**
     * @param \Illuminate\View\View $view
     */
    public function compose(View $view)
    {
        try {
            $w = [
                'enabled' => true,
            ];

            // If not logged in, then only get the public pages
            if (!Auth::check()) {
                $w['public'] = true;
            }

            $pages = $this->pageRepo->findWhere($w, static::$fields);
        } catch (Exception $e) {
            $pages = [];
        }

        $view->with('page_links', $pages);
    }
}
