<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Http\Controllers\Admin\Traits\Importable;
use App\Http\Requests\CreateFareRequest;
use App\Http\Requests\UpdateFareRequest;
use App\Models\Enums\FareType;
use App\Models\Enums\ImportExportType;
use App\Repositories\FareRepository;
use App\Services\ExportService;
use App\Services\ImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;
use Prettus\Repository\Criteria\RequestCriteria;

class FareController extends Controller
{
    use Importable;

    private FareRepository $fareRepo;
    private ImportService $importSvc;

    /**
     * FareController constructor.
     *
     * @param FareRepository $fareRepo
     * @param ImportService  $importSvc
     */
    public function __construct(
        FareRepository $fareRepo,
        ImportService $importSvc
    ) {
        $this->fareRepo = $fareRepo;
        $this->importSvc = $importSvc;
    }

    /**
     * Display a listing of the Fare.
     *
     * @param Request $request
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $this->fareRepo->pushCriteria(new RequestCriteria($request));
        $fares = $this->fareRepo->all();

        return view('admin.fares.index')
            ->with('fares', $fares);
    }

    /**
     * Show the form for creating a new Fare.
     */
    public function create()
    {
        return view('admin.fares.create', [
            'fare_types' => FareType::select(),
        ]);
    }

    /**
     * Store a newly created Fare in storage.
     *
     * @param CreateFareRequest $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return mixed
     */
    public function store(CreateFareRequest $request)
    {
        $input = $request->all();
        $fare = $this->fareRepo->create($input);

        Flash::success('Fare saved successfully.');
        return redirect(route('admin.fares.index'));
    }

    /**
     * Display the specified Fare.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function show($id)
    {
        $fare = $this->fareRepo->findWithoutFail($id);
        if (empty($fare)) {
            Flash::error('Fare not found');
            return redirect(route('admin.fares.index'));
        }

        return view('admin.fares.show', [
            'fare' => $fare,
        ]);
    }

    /**
     * Show the form for editing the specified Fare.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function edit($id)
    {
        $fare = $this->fareRepo->findWithoutFail($id);
        if (empty($fare)) {
            Flash::error('Fare not found');
            return redirect(route('admin.fares.index'));
        }

        return view('admin.fares.edit', [
            'fare'       => $fare,
            'fare_types' => FareType::select(),
        ]);
    }

    /**
     * Update the specified Fare in storage.
     *
     * @param int               $id
     * @param UpdateFareRequest $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return mixed
     */
    public function update($id, UpdateFareRequest $request)
    {
        $fare = $this->fareRepo->findWithoutFail($id);
        if (empty($fare)) {
            Flash::error('Fare not found');
            return redirect(route('admin.fares.index'));
        }

        $fare = $this->fareRepo->update($request->all(), $id);

        Flash::success('Fare updated successfully.');
        return redirect(route('admin.fares.index'));
    }

    /**
     * Remove the specified Fare from storage.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function destroy($id)
    {
        $fare = $this->fareRepo->findWithoutFail($id);
        if (empty($fare)) {
            Flash::error('Fare not found');
            return redirect(route('admin.fares.index'));
        }

        Log::info('Deleting fare "'.$fare->name.'", id='.$fare->id);

        $this->fareRepo->delete($id);

        Flash::success('Fare deleted successfully.');
        return redirect(route('admin.fares.index'));
    }

    /**
     * Run the aircraft exporter
     *
     * @param Request $request
     *
     * @throws \League\Csv\Exception
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $exporter = app(ExportService::class);
        $fares = $this->fareRepo->all();

        $path = $exporter->exportFares($fares);
        return response()
            ->download($path, 'fares.csv', [
                'content-type' => 'text/csv',
            ])
            ->deleteFileAfterSend(true);
    }

    /**
     * @param Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function import(Request $request)
    {
        $logs = [
            'success' => [],
            'errors'  => [],
        ];

        if ($request->isMethod('post')) {
            $logs = $this->importFile($request, ImportExportType::FARES);
        }

        return view('admin.fares.import', [
            'logs' => $logs,
        ]);
    }
}
