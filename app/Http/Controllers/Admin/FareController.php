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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Laracasts\Flash\Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FareController extends Controller
{
    use Importable;

    /**
     * FareController constructor.
     *
     * @param FareRepository $fareRepo
     * @param ImportService  $importSvc
     */
    public function __construct(
        private readonly FareRepository $fareRepo,
        private readonly ImportService $importSvc
    ) {
    }

    /**
     * Display a listing of the Fare.
     *
     * @param Request $request
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     *
     * @return View
     */
    public function index(Request $request): View
    {
        $this->fareRepo->pushCriteria(new RequestCriteria($request));
        $fares = $this->fareRepo->all();

        return view('admin.fares.index')
            ->with('fares', $fares);
    }

    /**
     * Show the form for creating a new Fare.
     */
    public function create(): View
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
     * @return RedirectResponse
     */
    public function store(CreateFareRequest $request): RedirectResponse
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
     * @return RedirectResponse|View
     */
    public function show(int $id): RedirectResponse|View
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
     * @return RedirectResponse|View
     */
    public function edit(int $id): RedirectResponse|View
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
     * @return RedirectResponse
     */
    public function update(int $id, UpdateFareRequest $request): RedirectResponse
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
    public function destroy(int $id): RedirectResponse
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
     * @return BinaryFileResponse
     */
    public function export(Request $request): BinaryFileResponse
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
     * @return View
     */
    public function import(Request $request): View
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
