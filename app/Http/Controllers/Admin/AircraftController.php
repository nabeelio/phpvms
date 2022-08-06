<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Http\Controllers\Admin\Traits\Importable;
use App\Http\Requests\CreateAircraftRequest;
use App\Http\Requests\UpdateAircraftRequest;
use App\Models\Aircraft;
use App\Models\Enums\AircraftStatus;
use App\Models\Enums\ImportExportType;
use App\Models\Expense;
use App\Models\Subfleet;
use App\Repositories\AircraftRepository;
use App\Repositories\AirportRepository;
use App\Services\ExportService;
use App\Services\ImportService;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;

class AircraftController extends Controller
{
    use Importable;

    private AircraftRepository $aircraftRepo;
    private AirportRepository $airportRepo;
    private ImportService $importSvc;

    /**
     * AircraftController constructor.
     *
     * @param AirportRepository  $airportRepo
     * @param AircraftRepository $aircraftRepo
     * @param ImportService      $importSvc
     */
    public function __construct(
        AirportRepository $airportRepo,
        AircraftRepository $aircraftRepo,
        ImportService $importSvc
    ) {
        $this->aircraftRepo = $aircraftRepo;
        $this->airportRepo = $airportRepo;
        $this->importSvc = $importSvc;
    }

    /**
     * Display a listing of the Aircraft.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // If subfleet ID is passed part of the query string, then only
        // show the aircraft that are in that subfleet
        $w = [];
        if ($request->filled('subfleet')) {
            $w['subfleet_id'] = $request->input('subfleet');
        }

        $aircraft = $this->aircraftRepo->with(['subfleet'])->whereOrder($w, 'registration', 'asc');
        $aircraft = $aircraft->all();

        return view('admin.aircraft.index', [
            'aircraft'    => $aircraft,
            'subfleet_id' => $request->input('subfleet'),
        ]);
    }

    /**
     * Show the form for creating a new Aircraft.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        return view('admin.aircraft.create', [
            'airports'    => $this->airportRepo->selectBoxList(),
            'hubs'        => $this->airportRepo->selectBoxList(true, true),
            'subfleets'   => Subfleet::all()->pluck('name', 'id'),
            'statuses'    => AircraftStatus::select(false),
            'subfleet_id' => $request->query('subfleet'),
        ]);
    }

    /**
     * Store a newly created Aircraft in storage.
     *
     * @param \App\Http\Requests\CreateAircraftRequest $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(CreateAircraftRequest $request)
    {
        $attrs = $request->all();
        $aircraft = $this->aircraftRepo->create($attrs);

        Flash::success('Aircraft saved successfully.');
        return redirect(route('admin.aircraft.edit', [$aircraft->id]));
    }

    /**
     * Display the specified Aircraft.
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function show($id)
    {
        $aircraft = $this->aircraftRepo->findWithoutFail($id);

        if (empty($aircraft)) {
            Flash::error('Aircraft not found');
            return redirect(route('admin.aircraft.index'));
        }

        return view('admin.aircraft.show', [
            'aircraft' => $aircraft,
        ]);
    }

    /**
     * Show the form for editing the specified Aircraft.
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function edit($id)
    {
        $aircraft = $this->aircraftRepo->findWithoutFail($id);

        if (empty($aircraft)) {
            Flash::error('Aircraft not found');
            return redirect(route('admin.aircraft.index'));
        }

        return view('admin.aircraft.edit', [
            'aircraft'  => $aircraft,
            'airports'  => $this->airportRepo->selectBoxList(),
            'hubs'      => $this->airportRepo->selectBoxList(true, true),
            'subfleets' => Subfleet::all()->pluck('name', 'id'),
            'statuses'  => AircraftStatus::select(false),
        ]);
    }

    /**
     * Update the specified Aircraft in storage.
     *
     * @param mixed                 $id
     * @param UpdateAircraftRequest $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return mixed
     */
    public function update($id, UpdateAircraftRequest $request)
    {
        /** @var \App\Models\Aircraft $aircraft */
        $aircraft = $this->aircraftRepo->findWithoutFail($id);

        if (empty($aircraft)) {
            Flash::error('Aircraft not found');
            return redirect(route('admin.aircraft.index'));
        }

        $attrs = $request->all();
        $this->aircraftRepo->update($attrs, $id);

        Flash::success('Aircraft updated successfully.');
        return redirect(route('admin.aircraft.index').'?subfleet='.$aircraft->subfleet_id);
    }

    /**
     * Remove the specified Aircraft from storage.
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function destroy($id)
    {
        /** @var \App\Models\Aircraft $aircraft */
        $aircraft = $this->aircraftRepo->findWithoutFail($id);

        if (empty($aircraft)) {
            Flash::error('Aircraft not found');
            return redirect(route('admin.aircraft.index'));
        }

        $this->aircraftRepo->delete($id);

        Flash::success('Aircraft deleted successfully.');
        return redirect(route('admin.aircraft.index'));
    }

    /**
     * Run the aircraft exporter
     *
     * @param Request $request
     *
     * @throws \League\Csv\Exception
     *
     * @return mixed
     */
    public function export(Request $request)
    {
        $exporter = app(ExportService::class);

        $where = [];
        $file_name = 'aircraft.csv';
        if ($request->input('subfleet')) {
            $subfleet_id = $request->input('subfleet');
            $where['subfleet_id'] = $subfleet_id;
            $file_name = 'aircraft-'.$subfleet_id.'.csv';
        }

        $aircraft = $this->aircraftRepo->where($where)->orderBy('registration')->get();

        $path = $exporter->exportAircraft($aircraft);
        return response()->download($path, $file_name, ['content-type' => 'text/csv'])->deleteFileAfterSend(true);
    }

    /**
     * @param Request $request
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
            $logs = $this->importFile($request, ImportExportType::AIRCRAFT);
        }

        return view('admin.aircraft.import', [
            'logs' => $logs,
        ]);
    }

    /**
     * @param Aircraft|null $aircraft
     *
     * @return mixed
     */
    protected function return_expenses_view(Aircraft $aircraft)
    {
        $aircraft->refresh();

        return view('admin.aircraft.expenses', [
            'aircraft' => $aircraft,
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
        $aircraft = $this->aircraftRepo->findWithoutFail($id);
        if (empty($aircraft)) {
            return $this->return_expenses_view($aircraft);
        }

        if ($request->isMethod('get')) {
            return $this->return_expenses_view($aircraft);
        }

        if ($request->isMethod('post')) {
            $expense = new Expense($request->post());
            $expense->ref_model = Aircraft::class;
            $expense->ref_model_id = $aircraft->id;
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

        return $this->return_expenses_view($aircraft);
    }
}
