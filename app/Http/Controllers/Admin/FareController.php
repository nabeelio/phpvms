<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateFareRequest;
use App\Http\Requests\ImportRequest;
use App\Http\Requests\UpdateFareRequest;
use App\Interfaces\Controller;
use App\Repositories\FareRepository;
use App\Services\ExportService;
use App\Services\ImportService;
use Flash;
use Illuminate\Http\Request;
use Log;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Storage;

/**
 * Class FareController
 * @package App\Http\Controllers\Admin
 */
class FareController extends Controller
{
    private $fareRepo,
            $importSvc;

    /**
     * FareController constructor.
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
     * @param Request $request
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
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
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.fares.create');
    }

    /**
     * Store a newly created Fare in storage.
     * @param CreateFareRequest $request
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
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
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $fare = $this->fareRepo->findWithoutFail($id);
        if (empty($fare)) {
            Flash::error('Fare not found');
            return redirect(route('admin.fares.index'));
        }

        return view('admin.fares.show')->with('fare', $fare);
    }

    /**
     * Show the form for editing the specified Fare.
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $fare = $this->fareRepo->findWithoutFail($id);
        if (empty($fare)) {
            Flash::error('Fare not found');
            return redirect(route('admin.fares.index'));
        }

        return view('admin.fares.edit')->with('fare', $fare);
    }

    /**
     * Update the specified Fare in storage.
     * @param  int              $id
     * @param UpdateFareRequest $request
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
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
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $fare = $this->fareRepo->findWithoutFail($id);
        if (empty($fare)) {
            Flash::error('Fare not found');
            return redirect(route('admin.fares.index'));
        }

        $this->fareRepo->delete($id);

        Flash::success('Fare deleted successfully.');
        return redirect(route('admin.fares.index'));
    }

    /**
     * Run the aircraft exporter
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \League\Csv\Exception
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
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Validation\ValidationException
     */
    public function import(Request $request)
    {
        $logs = [
            'success' => [],
            'errors'  => [],
        ];

        if ($request->isMethod('post')) {
            ImportRequest::validate($request);
            $path = Storage::putFileAs(
                'import', $request->file('csv_file'), 'import_fares.csv'
            );

            $path = storage_path('app/'.$path);
            Log::info('Uploaded fares import file to '.$path);
            $logs = $this->importSvc->importFares($path);
        }

        return view('admin.fares.import', [
            'logs' => $logs,
        ]);
    }
}
