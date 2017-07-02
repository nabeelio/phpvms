<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateRankRequest;
use App\Http\Requests\UpdateRankRequest;
use App\Repositories\RankRepository;
use App\Repositories\SubfleetRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class RankController extends BaseController
{
    /** @var  RankRepository */
    private $rankRepository, $subfleetRepo;

    public function __construct(
        RankRepository $rankingRepo,
        SubfleetRepository $subfleetRepo
    )
    {
        $this->rankRepository = $rankingRepo;
        $this->subfleetRepo = $subfleetRepo;
    }

    protected function getAvailSubfleets($rank)
    {
        $retval = [];
        $all_subfleets = $this->subfleetRepo->all();
        $avail_subfleets = $all_subfleets->except($rank->subfleets->modelKeys());
        foreach ($avail_subfleets as $subfleet) {
            $retval[$subfleet->id] = $subfleet->name.
                ' (airline: '.$subfleet->airline->code.')';
        }

        return $retval;
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

        return view('admin.ranks.index', [
            'ranks' => $ranks,
        ]);
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
     * @param CreateRankRequest $request
     * @return Response
     */
    public function store(CreateRankRequest $request)
    {
        $input = $request->all();

        $model = $this->rankRepository->create($input);
        Flash::success('Ranking saved successfully.');

        $ranks = $this->rankRepository->all();
        return view('admin.ranks.table', [
            'ranks' => $ranks,
        ]);
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

        return view('admin.ranks.show', [
            'rank' => $rank
        ]);
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

        $avail_subfleets = $this->getAvailSubfleets($rank);
        return view('admin.ranks.edit', [
            'rank' => $rank,
            'avail_subfleets' => $avail_subfleets,
        ]);
    }

    /**
     * Update the specified Ranking in storage.
     *
     * @param  int              $id
     * @param UpdateRankRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateRankRequest $request)
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

    protected function return_subfleet_view($rank)
    {
        $avail_subfleets = $this->getAvailSubfleets($rank);
        return view('admin.ranks.subfleets', [
            'rank' => $rank,
            'avail_subfleets' => $avail_subfleets,
        ]);
    }

    public function subfleets(Request $request)
    {
        $id = $request->id;

        $rank = $this->rankRepository->findWithoutFail($id);
        if (empty($rank)) {
            Flash::error('Rank not found!');
            return redirect(route('admin.ranks.index'));
        }

        // add aircraft to flight
        if ($request->isMethod('post')) {
            $rank->subfleets()->syncWithoutDetaching([$request->subfleet_id]);
        }

        // remove aircraft from flight
        elseif ($request->isMethod('delete')) {
            $rank->subfleets()->detach($request->subfleet_id);
        }

        return $this->return_subfleet_view($rank);
    }
}
