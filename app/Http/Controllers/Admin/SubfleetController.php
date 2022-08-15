<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Http\Controllers\Admin\Traits\Importable;
use App\Http\Requests\CreateSubfleetRequest;
use App\Http\Requests\UpdateSubfleetRequest;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Enums\FareType;
use App\Models\Enums\FuelType;
use App\Models\Enums\ImportExportType;
use App\Models\Expense;
use App\Models\Subfleet;
use App\Repositories\AircraftRepository;
use App\Repositories\FareRepository;
use App\Repositories\RankRepository;
use App\Repositories\SubfleetRepository;
use App\Repositories\TypeRatingRepository;
use App\Services\ExportService;
use App\Services\FareService;
use App\Services\FleetService;
use App\Services\ImportService;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Prettus\Repository\Criteria\RequestCriteria;

class SubfleetController extends Controller
{
    use Importable;

    private AircraftRepository $aircraftRepo;
    private FareRepository $fareRepo;
    private FareService $fareSvc;
    private FleetService $fleetSvc;
    private ImportService $importSvc;
    private RankRepository $rankRepo;
    private SubfleetRepository $subfleetRepo;
    private TypeRatingRepository $typeratingRepo;

    /**
     * SubfleetController constructor.
     *
     * @param AircraftRepository   $aircraftRepo
     * @param FareRepository       $fareRepo
     * @param FareService          $fareSvc
     * @param FleetService         $fleetSvc
     * @param ImportService        $importSvc
     * @param RankRepository       $rankRepo
     * @param SubfleetRepository   $subfleetRepo
     * @param TypeRatingRepository $typeratingRepo
     */
    public function __construct(
        AircraftRepository $aircraftRepo,
        FareRepository $fareRepo,
        FareService $fareSvc,
        FleetService $fleetSvc,
        ImportService $importSvc,
        RankRepository $rankRepo,
        SubfleetRepository $subfleetRepo,
        TypeRatingRepository $typeratingRepo
    ) {
        $this->aircraftRepo = $aircraftRepo;
        $this->fareRepo = $fareRepo;
        $this->fareSvc = $fareSvc;
        $this->fleetSvc = $fleetSvc;
        $this->importSvc = $importSvc;
        $this->rankRepo = $rankRepo;
        $this->subfleetRepo = $subfleetRepo;
        $this->typeratingRepo = $typeratingRepo;
    }

    /**
     * Display a listing of the Subfleet.
     *
     * @param Request $request
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->subfleetRepo->with(['airline'])->pushCriteria(new RequestCriteria($request));
        $subfleets = $this->subfleetRepo->orderby('name', 'asc')->get();

        return view('admin.subfleets.index', [
            'subfleets' => $subfleets,
        ]);
    }

    /**
     * Show the form for creating a new Subfleet.
     */
    public function create()
    {
        return view('admin.subfleets.create', [
            'airlines'   => Airline::all()->pluck('name', 'id'),
            'hubs'       => Airport::where('hub', 1)->pluck('name', 'id'),
            'fuel_types' => FuelType::labels(),
        ]);
    }

    /**
     * Store a newly created Subfleet in storage.
     *
     * @param CreateSubfleetRequest $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(CreateSubfleetRequest $request)
    {
        $input = $request->all();
        $subfleet = $this->subfleetRepo->create($input);
        Flash::success('Subfleet saved successfully.');

        return redirect(route('admin.subfleets.edit', [$subfleet->id]));
    }

    /**
     * Display the specified Subfleet.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function show($id)
    {
        $subfleet = $this->subfleetRepo
            ->with(['fares'])
            ->findWithoutFail($id);

        if (empty($subfleet)) {
            Flash::error('Subfleet not found');

            return redirect(route('admin.subfleets.index'));
        }

        $avail_fares = $this->getAvailFares($subfleet);

        return view('admin.subfleets.show', [
            'subfleet'    => $subfleet,
            'avail_fares' => $avail_fares,
        ]);
    }

    /**
     * Show the form for editing the specified Subfleet.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function edit($id)
    {
        $subfleet = $this->subfleetRepo
            ->with(['fares', 'ranks', 'typeratings'])
            ->findWithoutFail($id);

        if (empty($subfleet)) {
            Flash::error('Subfleet not found');

            return redirect(route('admin.subfleets.index'));
        }

        $avail_fares = $this->getAvailFares($subfleet);
        $avail_ranks = $this->getAvailRanks($subfleet);
        $avail_ratings = $this->getAvailTypeRatings($subfleet);

        return view('admin.subfleets.edit', [
            'airlines'      => Airline::all()->pluck('name', 'id'),
            'hubs'          => Airport::where('hub', 1)->pluck('name', 'id'),
            'fuel_types'    => FuelType::labels(),
            'avail_fares'   => $avail_fares,
            'avail_ranks'   => $avail_ranks,
            'avail_ratings' => $avail_ratings,
            'subfleet'      => $subfleet,
        ]);
    }

    /**
     * Update the specified Subfleet in storage.
     *
     * @param int                   $id
     * @param UpdateSubfleetRequest $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return mixed
     */
    public function update($id, UpdateSubfleetRequest $request)
    {
        $subfleet = $this->subfleetRepo->findWithoutFail($id);

        if (empty($subfleet)) {
            Flash::error('Subfleet not found');

            return redirect(route('admin.subfleets.index'));
        }

        $this->subfleetRepo->update($request->all(), $id);
        Flash::success('Subfleet updated successfully.');

        return redirect(route('admin.subfleets.index'));
    }

    /**
     * Remove the specified Subfleet from storage.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function destroy($id)
    {
        $subfleet = $this->subfleetRepo->findWithoutFail($id);

        if (empty($subfleet)) {
            Flash::error('Subfleet not found');

            return redirect(route('admin.subfleets.index'));
        }

        // Make sure no aircraft are assigned to this subfleet
        // before trying to delete it, or else things might go boom
        $aircraft = $this->aircraftRepo->findWhere(['subfleet_id' => $id], ['id']);
        if ($aircraft->count() > 0) {
            Flash::error('There are aircraft still assigned to this subfleet, you can\'t delete it!')->important();

            return redirect(route('admin.subfleets.index'));
        }

        $this->subfleetRepo->delete($id);
        Flash::success('Subfleet deleted successfully.');

        return redirect(route('admin.subfleets.index'));
    }

    /**
     * Run the subfleet exporter
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $exporter = app(ExportService::class);
        $subfleets = $this->subfleetRepo->all();

        $path = $exporter->exportSubfleets($subfleets);

        return response()->download($path, 'subfleets.csv', ['content-type' => 'text/csv'])->deleteFileAfterSend(true);
    }

    /**
     * @param Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return mixed
     */
    public function import(Request $request)
    {
        $logs = [
            'success' => [],
            'errors'  => [],
        ];

        if ($request->isMethod('post')) {
            $logs = $this->importFile($request, ImportExportType::SUBFLEETS);
        }

        return view('admin.subfleets.import', [
            'logs' => $logs,
        ]);
    }

    /**
     * Get all the fares that haven't been assigned to a given subfleet
     *
     * @param mixed $subfleet
     *
     * @return array
     */
    protected function getAvailFares($subfleet)
    {
        $retval = [];
        $all_fares = $this->fareRepo->all();
        $avail_fares = $all_fares->except($subfleet->fares->modelKeys());
        foreach ($avail_fares as $fare) {
            $retval[$fare->id] = $fare->name.
                ' (price: '.$fare->price.
                ', type: '.FareType::label($fare->type).
                ', cost: '.$fare->cost.
                ', capacity: '.$fare->capacity.')';
        }

        return $retval;
    }

    /**
     * Get the ranks that are available to the subfleet
     *
     * @param $subfleet
     *
     * @return array
     */
    protected function getAvailRanks($subfleet)
    {
        $retval = [];
        $all_ranks = $this->rankRepo->all();
        $avail_ranks = $all_ranks->except($subfleet->ranks->modelKeys());
        foreach ($avail_ranks as $rank) {
            $retval[$rank->id] = $rank->name;
        }

        return $retval;
    }

    /**
     * Get the type ratings that are available to the subfleet
     *
     * @param $subfleet
     *
     * @return array
     */
    protected function getAvailTypeRatings($subfleet)
    {
        $retval = [];
        $all_ratings = $this->typeratingRepo->all();
        $avail_ratings = $all_ratings->except($subfleet->typeratings->modelKeys());
        foreach ($avail_ratings as $tr) {
            $retval[$tr->id] = $tr->name.' ('.$tr->type.')';
        }

        return $retval;
    }

    /**
     * @param Subfleet $subfleet
     *
     * @return mixed
     */
    protected function return_expenses_view(?Subfleet $subfleet)
    {
        $subfleet->refresh();
        return view('admin.subfleets.expenses', [
            'subfleet' => $subfleet,
        ]);
    }

    /**
     * @param Subfleet $subfleet
     *
     * @return mixed
     */
    protected function return_fares_view(?Subfleet $subfleet)
    {
        $subfleet->refresh();
        $avail_fares = $this->getAvailFares($subfleet);

        return view('admin.subfleets.fares', [
            'subfleet'    => $subfleet,
            'avail_fares' => $avail_fares,
        ]);
    }

    /**
     * @param Subfleet $subfleet
     *
     * @return mixed
     */
    protected function return_ranks_view(?Subfleet $subfleet)
    {
        $subfleet->refresh();
        $avail_ranks = $this->getAvailRanks($subfleet);

        return view('admin.subfleets.ranks', [
            'subfleet'    => $subfleet,
            'avail_ranks' => $avail_ranks,
        ]);
    }

    /**
     * @param Subfleet $subfleet
     *
     * @return mixed
     */
    protected function return_typeratings_view(?Subfleet $subfleet)
    {
        $subfleet->refresh();
        $avail_ratings = $this->getAvailTypeRatings($subfleet);

        return view('admin.subfleets.type_ratings', [
            'subfleet'      => $subfleet,
            'avail_ratings' => $avail_ratings,
        ]);
    }

    /**
     * Operations for associating ranks to the subfleet
     *
     * @param         $id
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function expenses($id, Request $request)
    {
        $subfleet = $this->subfleetRepo->findWithoutFail($id);
        if (empty($subfleet)) {
            return $this->return_expenses_view($subfleet);
        }

        if ($request->isMethod('get')) {
            return $this->return_expenses_view($subfleet);
        }

        /*
         * update specific rank data
         */
        if ($request->isMethod('post')) {
            $expense = new Expense($request->post());
            $expense->ref_model = Subfleet::class;
            $expense->ref_model_id = $subfleet->id;
            $expense->save();
        } elseif ($request->isMethod('put')) {
            $expense = Expense::findOrFail($request->input('expense_id'));
            $expense->{$request->name} = $request->value;
            $expense->save();
        } // dissassociate fare from teh aircraft
        elseif ($request->isMethod('delete')) {
            $expense = Expense::findOrFail($request->input('expense_id'));
            $expense->delete();
        }

        return $this->return_expenses_view($subfleet);
    }

    /**
     * Operations on fares to the subfleet
     *
     * @param         $id
     * @param Request $request
     *
     * @return mixed
     */
    public function fares($id, Request $request)
    {
        $subfleet = $this->subfleetRepo->findWithoutFail($id);
        if (empty($subfleet)) {
            return $this->return_fares_view($subfleet);
        }

        if ($request->isMethod('get')) {
            return $this->return_fares_view($subfleet);
        }

        /*
         * update specific fare data
         */
        if ($request->isMethod('post')) {
            $fare = $this->fareRepo->find($request->fare_id);
            $this->fareSvc->setForSubfleet($subfleet, $fare);
        } // update the pivot table with overrides for the fares
        elseif ($request->isMethod('put')) {
            $override = [];
            $fare = $this->fareRepo->find($request->fare_id);
            $override[$request->name] = $request->value;
            $this->fareSvc->setForSubfleet($subfleet, $fare, $override);
        } // dissassociate fare from teh aircraft
        elseif ($request->isMethod('delete')) {
            $fare = $this->fareRepo->find($request->fare_id);
            $this->fareSvc->delFareFromSubfleet($subfleet, $fare);
        }

        return $this->return_fares_view($subfleet);
    }

    /**
     * Operations for associating ranks to the subfleet
     *
     * @param         $id
     * @param Request $request
     *
     * @return mixed
     */
    public function ranks($id, Request $request)
    {
        $subfleet = $this->subfleetRepo->findWithoutFail($id);
        if (empty($subfleet)) {
            return $this->return_ranks_view($subfleet);
        }

        if ($request->isMethod('get')) {
            return $this->return_ranks_view($subfleet);
        }

        // associate rank with the subfleet
        if ($request->isMethod('post')) {
            $rank = $this->rankRepo->find($request->input('rank_id'));
            $this->fleetSvc->addSubfleetToRank($subfleet, $rank);
        } // override definitions
        elseif ($request->isMethod('put')) {
            $override = [];
            $rank = $this->rankRepo->find($request->input('rank_id'));
            $override[$request->name] = $request->value;

            $this->fleetSvc->addSubfleetToRank($subfleet, $rank, $override);
        } // dissassociate rank from the subfleet
        elseif ($request->isMethod('delete')) {
            $rank = $this->rankRepo->find($request->input('rank_id'));
            $this->fleetSvc->removeSubfleetFromRank($subfleet, $rank);
        }

        $subfleet->save();

        return $this->return_ranks_view($subfleet);
    }

    /**
     * Operations for associating type ratings to the subfleet
     *
     * @param         $id
     * @param Request $request
     *
     * @return mixed
     */
    public function typeratings($id, Request $request)
    {
        $subfleet = $this->subfleetRepo->findWithoutFail($id);
        if (empty($subfleet)) {
            return $this->return_typeratings_view($subfleet);
        }

        if ($request->isMethod('get')) {
            return $this->return_typeratings_view($subfleet);
        }

        // associate subfleet with type rating
        if ($request->isMethod('post')) {
            $typerating = $this->typeratingRepo->find($request->input('typerating_id'));
            $this->fleetSvc->addSubfleetToTypeRating($subfleet, $typerating);
        } // dissassociate subfleet from the type rating
        elseif ($request->isMethod('delete')) {
            $typerating = $this->typeratingRepo->find($request->input('typerating_id'));
            $this->fleetSvc->removeSubfleetFromTypeRating($subfleet, $typerating);
        }

        $subfleet->save();

        return $this->return_typeratings_view($subfleet);
    }
}
