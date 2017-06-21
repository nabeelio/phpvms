<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateRankingRequest;
use App\Http\Requests\UpdateRankingRequest;
use App\Repositories\RankRepository;
use App\Http\Controllers\AppBaseController as InfyOmBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class RankController extends BaseController
{
    /** @var  RankRepository */
    private $rankRepository;

    public function __construct(RankRepository $rankingRepo)
    {
        $this->rankRepository = $rankingRepo;
    }

    /**
     * Display a listing of the Ranking.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->rankRepository->pushCriteria(new RequestCriteria($request));
        $ranks = $this->rankRepository->all();

        return view('admin.ranks.index')
            ->with('ranks', $ranks);
    }

    /**
     * Show the form for creating a new Ranking.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.ranks.create');
    }

    /**
     * Store a newly created Ranking in storage.
     *
     * @param CreateRankingRequest $request
     *
     * @return Response
     */
    public function store(CreateRankingRequest $request)
    {
        $input = $request->all();
        $rank = $this->rankRepository->create($input);

        Flash::success('Ranking saved successfully.');

        return redirect(route('admin.ranks.index'));
    }

    /**
     * Display the specified Ranking.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $rank = $this->rankRepository->findWithoutFail($id);

        if (empty($rank)) {
            Flash::error('Ranking not found');
            return redirect(route('admin.ranks.index'));
        }

        return view('admin.ranks.show')->with('rank', $rank);
    }

    /**
     * Show the form for editing the specified Ranking.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $rank = $this->rankRepository->findWithoutFail($id);

        if (empty($rank)) {
            Flash::error('Ranking not found');
            return redirect(route('admin.ranks.index'));
        }

        return view('admin.ranks.edit')->with('rank', $rank);
    }

    /**
     * Update the specified Ranking in storage.
     *
     * @param  int              $id
     * @param UpdateRankingRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateRankingRequest $request)
    {
        $rank = $this->rankRepository->findWithoutFail($id);

        if (empty($rank)) {
            Flash::error('Ranking not found');
            return redirect(route('admin.ranks.index'));
        }

        $rank = $this->rankRepository->update($request->all(), $id);

        Flash::success('Ranking updated successfully.');

        return redirect(route('admin.ranks.index'));
    }

    /**
     * Remove the specified Ranking from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $rank = $this->rankRepository->findWithoutFail($id);

        if (empty($rank)) {
            Flash::error('Ranking not found');
            return redirect(route('admin.ranks.index'));
        }

        $this->rankRepository->delete($id);

        Flash::success('Ranking deleted successfully.');

        return redirect(route('admin.ranks.index'));
    }
}
