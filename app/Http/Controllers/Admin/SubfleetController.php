<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Models\Airline;
use App\Models\Subfleet;
use App\Http\Requests\CreateSubfleetRequest;
use App\Http\Requests\UpdateSubfleetRequest;
use App\Models\Fare;
use App\Repositories\FareRepository;
use App\Repositories\SubfleetRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class SubfleetController extends BaseController
{
    /** @var  SubfleetRepository */
    private $subfleetRepo, $fareRepo;

    /**
     * SubfleetController constructor.
     *
     * @param SubfleetRepository $subfleetRepo
     * @param FareRepository     $fareRepo
     */
    public function __construct(
        SubfleetRepository $subfleetRepo,
        FareRepository $fareRepo
    ) {
        $this->subfleetRepo = $subfleetRepo;
        $this->fareRepo = $fareRepo;
    }

    /**
     * @return array
     */
    protected function getFuelTypes()
    {
        $retval = [];
        foreach (config('enums.fuel_types') as $fuel_type => $value) {
            $retval[$value] = $fuel_type;
        }

        return $retval;
    }

    /**
     * Get all the fares that haven't been assigned to a given subfleet
     */
    protected function getAvailFares($subfleet)
    {
        $retval = [];
        $all_fares = $this->fareRepo->all();
        $avail_fares = $all_fares->except($subfleet->fares->modelKeys());
        foreach ($avail_fares as $fare) {
            $retval[$fare->id] = $fare->name.
                ' (price: '.$fare->price.
                ', cost: '.$fare->cost.
                ', capacity: '.$fare->capacity.')';
        }

        return $retval;
    }

    /**
     * Display a listing of the Subfleet.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->subfleetRepo->pushCriteria(new RequestCriteria($request));
        $subfleets = $this->subfleetRepo->all();

        return view('admin.subfleets.index', [
            'subfleets' => $subfleets,
        ]);
    }

    /**
     * Show the form for creating a new Subfleet.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.subfleets.create', [
            'airlines' => Airline::all()->pluck('name', 'id'),
            'fuel_types' => $this->getFuelTypes(),
        ]);
    }

    /**
     * Store a newly created Subfleet in storage.
     *
     * @param CreateSubfleetRequest $request
     *
     * @return Response
     */
    public function store(CreateSubfleetRequest $request)
    {
        $input = $request->all();
        $subfleet = $this->subfleetRepo->create($input);

        Flash::success('Subfleet saved successfully.');
        return redirect(route('admin.subfleets.index'));
    }

    /**
     * Display the specified Subfleet.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $subfleet = $this->subfleetRepo->findWithoutFail($id);

        if (empty($subfleet)) {
            Flash::error('Subfleet not found');
            return redirect(route('admin.subfleets.index'));
        }

        $avail_fares = $this->getAvailFares($subfleet);
        return view('admin.subfleets.show', [
            'subfleet' => $subfleet,
            'avail_fares' => $avail_fares,
        ]);
    }

    /**
     * Show the form for editing the specified Subfleet.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $subfleet = $this->subfleetRepo->findWithoutFail($id);

        if (empty($subfleet)) {
            Flash::error('Subfleet not found');
            return redirect(route('admin.subfleets.index'));
        }

        $avail_fares = $this->getAvailFares($subfleet);
        return view('admin.subfleets.edit', [
            'airlines' => Airline::all()->pluck('name', 'id'),
            'fuel_types' => $this->getFuelTypes(),
            'avail_fares' => $avail_fares,
            'subfleet' => $subfleet,
        ]);
    }

    /**
     * Update the specified Subfleet in storage.
     *
     * @param  int              $id
     * @param UpdateSubfleetRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSubfleetRequest $request)
    {
        $subfleet = $this->subfleetRepo->findWithoutFail($id);

        if (empty($subfleet)) {
            Flash::error('Subfleet not found');
            return redirect(route('admin.subfleets.index'));
        }

        $subfleet = $this->subfleetRepo->update($request->all(), $id);

        Flash::success('Subfleet updated successfully.');
        return redirect(route('admin.subfleets.index'));
    }

    /**
     * Remove the specified Subfleet from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $subfleet = $this->subfleetRepo->findWithoutFail($id);

        if (empty($subfleet)) {
            Flash::error('Subfleet not found');
            return redirect(route('admin.subfleets.index'));
        }

        $this->subfleetRepo->delete($id);

        Flash::success('Subfleet deleted successfully.');
        return redirect(route('admin.subfleets.index'));
    }

    protected function return_fares_view(Subfleet $subfleet)
    {
        $subfleet->refresh();
        $avail_fares = $this->getAvailFares($subfleet);

        return view('admin.subfleets.fares', [
            'subfleet'    => $subfleet,
            'avail_fares' => $avail_fares,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function fares(Request $request)
    {
        $id = $request->id;

        $subfleet = $this->subfleetRepo->findWithoutFail($id);
        if (empty($subfleet)) {
            return $this->return_fares_view($subfleet);
            //return view('admin.aircraft.fares', ['fares' => []]);
        }

        $fare_svc = app('App\Services\FareService');

        if ($request->isMethod('get')) {
            return $this->return_fares_view($subfleet);
        }

        /**
         * update specific fare data
         */
        if ($request->isMethod('post')) {
            $fare = $this->fareRepo->findWithoutFail($request->fare_id);
            $fare_svc->setForSubfleet($subfleet, $fare);
        }

        // update the pivot table with overrides for the fares
        elseif ($request->isMethod('put')) {
            $override = [];
            $fare = $this->fareRepo->findWithoutFail($request->fare_id);
            $override[$request->name] = $request->value;
            $fare_svc->setForSubfleet($subfleet, $fare, $override);
        }

        // dissassociate fare from teh aircraft
        elseif ($request->isMethod('delete')) {
            $fare = $this->fareRepo->findWithoutFail($request->fare_id);
            $fare_svc->delFromAircraft($subfleet, $fare);
        }

        return $this->return_fares_view($subfleet);
    }
}
