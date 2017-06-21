<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\CreateRankingRequest;
use App\Http\Requests\UpdateRankingRequest;
use App\Repositories\RankingRepository;
use App\Http\Controllers\AppBaseController as InfyOmBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class RankingController extends InfyOmBaseController
{
    /** @var  RankingRepository */
    private $rankingRepository;

    public function __construct(RankingRepository $rankingRepo)
    {
        $this->rankingRepository = $rankingRepo;
    }

    /**
     * Display a listing of the Ranking.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->rankingRepository->pushCriteria(new RequestCriteria($request));
        $rankings = $this->rankingRepository->all();

        return view('admin.rankings.index')
            ->with('rankings', $rankings);
    }

    /**
     * Show the form for creating a new Ranking.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.rankings.create');
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

        $ranking = $this->rankingRepository->create($input);

        Flash::success('Ranking saved successfully.');

        return redirect(route('admin.rankings.index'));
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
        $ranking = $this->rankingRepository->findWithoutFail($id);

        if (empty($ranking)) {
            Flash::error('Ranking not found');

            return redirect(route('admin.rankings.index'));
        }

        return view('admin.rankings.show')->with('ranking', $ranking);
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
        $ranking = $this->rankingRepository->findWithoutFail($id);

        if (empty($ranking)) {
            Flash::error('Ranking not found');

            return redirect(route('admin.rankings.index'));
        }

        return view('admin.rankings.edit')->with('ranking', $ranking);
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
        $ranking = $this->rankingRepository->findWithoutFail($id);

        if (empty($ranking)) {
            Flash::error('Ranking not found');

            return redirect(route('admin.rankings.index'));
        }

        $ranking = $this->rankingRepository->update($request->all(), $id);

        Flash::success('Ranking updated successfully.');

        return redirect(route('admin.rankings.index'));
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
        $ranking = $this->rankingRepository->findWithoutFail($id);

        if (empty($ranking)) {
            Flash::error('Ranking not found');

            return redirect(route('admin.rankings.index'));
        }

        $this->rankingRepository->delete($id);

        Flash::success('Ranking deleted successfully.');

        return redirect(route('admin.rankings.index'));
    }
}
