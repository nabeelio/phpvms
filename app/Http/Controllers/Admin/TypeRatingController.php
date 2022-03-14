<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Http\Requests\CreateTypeRatingRequest;
use App\Http\Requests\UpdateTypeRatingRequest;
use App\Repositories\SubfleetRepository;
use App\Repositories\TypeRatingRepository;
use App\Services\FleetService;
use Cache;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Prettus\Repository\Criteria\RequestCriteria;

class TypeRatingController extends Controller
{
    private FleetService $fleetSvc;
    private SubfleetRepository $subfleetRepo;
    private TypeRatingRepository $typeratingRepo;

    public function __construct(
        FleetService $fleetSvc,
        SubfleetRepository $subfleetRepo,
        TypeRatingRepository $typeratingRepo
    ) {
        $this->fleetSvc = $fleetSvc;
        $this->subfleetRepo = $subfleetRepo;
        $this->typeratingRepo = $typeratingRepo;
    }

    public function index(Request $request)
    {
        $this->typeratingRepo->pushCriteria(new RequestCriteria($request));
        $typeratings = $this->typeratingRepo->orderby('type', 'asc')->get();

        return view('admin.typeratings.index', [
            'typeratings' => $typeratings,
        ]);
    }

    public function create()
    {
        return view('admin.typeratings.create');
    }

    public function store(CreateTypeRatingRequest $request)
    {
        $input = $request->all();

        $model = $this->typeratingRepo->create($input);
        Flash::success('Type Rating saved successfully.');

        return redirect(route('admin.typeratings.edit', [$model->id]));
    }

    public function show($id)
    {
        $typerating = $this->typeratingRepo->findWithoutFail($id);

        if (empty($typerating)) {
            Flash::error('Type Rating not found');

            return redirect(route('admin.typeratings.index'));
        }

        return view('admin.typeratings.show', [
            'typerating' => $typerating,
        ]);
    }

    public function edit($id)
    {
        $typerating = $this->typeratingRepo->findWithoutFail($id);

        if (empty($typerating)) {
            Flash::error('Type Rating not found');

            return redirect(route('admin.typeratings.index'));
        }

        $avail_subfleets = $this->getAvailSubfleets($typerating);

        return view('admin.typeratings.edit', [
            'typerating'      => $typerating,
            'avail_subfleets' => $avail_subfleets,
        ]);
    }

    public function update($id, UpdateTypeRatingRequest $request)
    {
        $typerating = $this->typeratingRepo->findWithoutFail($id);

        if (empty($typerating)) {
            Flash::error('Type Rating not found');

            return redirect(route('admin.typeratings.index'));
        }

        $typerating = $this->typeratingRepo->update($request->all(), $id);
        // Cache::forget(config('cache.keys.RANKS_PILOT_LIST.key'));
        Flash::success('Type Rating updated successfully.');

        return redirect(route('admin.typeratings.index'));
    }

    public function destroy($id)
    {
        $typerating = $this->typeratingRepo->findWithoutFail($id);

        if (empty($typerating)) {
            Flash::error('Type Rating not found');

            return redirect(route('admin.typeratings.index'));
        }

        $this->typeratingRepo->delete($id);

        Flash::success('Type Rating deleted successfully.');

        return redirect(route('admin.typeratings.index'));
    }

    protected function getAvailSubfleets($typerating)
    {
        $retval = [];
        $all_subfleets = $this->subfleetRepo->all();
        $avail_subfleets = $all_subfleets->except($typerating->subfleets->modelKeys());
        foreach ($avail_subfleets as $subfleet) {
            $retval[$subfleet->id] = $subfleet->name.' (Airline: '.$subfleet->airline->code.')';
        }

        return $retval;
    }

    protected function return_subfleet_view($typerating)
    {
        $avail_subfleets = $this->getAvailSubfleets($typerating);

        return view('admin.typeratings.subfleets', [
            'typerating'      => $typerating,
            'avail_subfleets' => $avail_subfleets,
        ]);
    }

    public function subfleets($id, Request $request)
    {
        $typerating = $this->typeratingRepo->findWithoutFail($id);
        if (empty($typerating)) {
            Flash::error('Type Rating not found!');
            return redirect(route('admin.typeratings.index'));
        }

        // add subfleet to type rating
        if ($request->isMethod('post')) {
            $subfleet = $this->subfleetRepo->find($request->input('subfleet_id'));
            $this->fleetSvc->addSubfleetToTypeRating($subfleet, $typerating);
        }
        // remove subfleet from type rating
        elseif ($request->isMethod('delete')) {
            $subfleet = $this->subfleetRepo->find($request->input('subfleet_id'));
            $this->fleetSvc->removeSubfleetFromTypeRating($subfleet, $typerating);
        }

        return $this->return_subfleet_view($typerating);
    }
}
