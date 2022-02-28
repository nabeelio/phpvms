<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Http\Requests\CreatePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Repositories\PageRepository;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;

class PagesController extends Controller
{
    private PageRepository $pageRepo;

    /**
     * @param PageRepository $pageRepo
     */
    public function __construct(PageRepository $pageRepo)
    {
        $this->pageRepo = $pageRepo;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $pages = $this->pageRepo->all();

        return view('admin.pages.index', [
            'pages' => $pages,
        ]);
    }

    /**
     * Show the form for creating a new Airlines.
     */
    public function create()
    {
        return view('admin.pages.create');
    }

    /**
     * Store a newly created Airlines in storage.
     *
     * @param \App\Http\Requests\CreatePageRequest $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(CreatePageRequest $request)
    {
        $input = $request->all();
        $this->pageRepo->create($input);

        Flash::success('Page saved successfully.');
        return redirect(route('admin.pages.index'));
    }

    /**
     * Display the specified page
     *
     * @param int $id
     *
     * @return mixed
     */
    public function show($id)
    {
        $pages = $this->pageRepo->findWithoutFail($id);

        if (empty($pages)) {
            Flash::error('Page not found');
            return redirect(route('admin.page.index'));
        }

        return view('admin.pages.show', [
            'pages' => $pages,
        ]);
    }

    /**
     * Show the form for editing the specified pages
     *
     * @param int $id
     *
     * @return mixed
     */
    public function edit($id)
    {
        $page = $this->pageRepo->findWithoutFail($id);

        if (empty($page)) {
            Flash::error('Page not found');
            return redirect(route('admin.pages.index'));
        }

        return view('admin.pages.edit', [
            'page' => $page,
        ]);
    }

    /**
     * Update the specified Airlines in storage.
     *
     * @param int               $id
     * @param UpdatePageRequest $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return mixed
     */
    public function update($id, UpdatePageRequest $request)
    {
        $page = $this->pageRepo->findWithoutFail($id);

        if (empty($page)) {
            Flash::error('page not found');
            return redirect(route('admin.pages.index'));
        }

        $this->pageRepo->update($request->all(), $id);

        Flash::success('pages updated successfully.');
        return redirect(route('admin.pages.index'));
    }

    /**
     * Remove the specified Airlines from storage.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function destroy($id)
    {
        $pages = $this->pageRepo->findWithoutFail($id);

        if (empty($pages)) {
            Flash::error('Page not found');
            return redirect(route('admin.pages.index'));
        }

        $this->pageRepo->delete($id);

        Flash::success('page deleted successfully.');
        return redirect(route('admin.pages.index'));
    }
}
