<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Http\Requests\CreateAirlineRequest;
use App\Http\Requests\UpdateAirlineRequest;
use App\Repositories\AirlineRepository;
use App\Services\AirlineService;
use App\Support\Countries;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laracasts\Flash\Flash;
use Prettus\Repository\Criteria\RequestCriteria;

class AirlinesController extends Controller
{
    /**
     * @param AirlineRepository $airlineRepo
     * @param AirlineService    $airlineSvc
     */
    public function __construct(
        private readonly AirlineRepository $airlineRepo,
        private readonly AirlineService $airlineSvc
    ) {
    }

    /**
     * Display a listing of the Airlines.
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request): View
    {
        $this->airlineRepo->pushCriteria(new RequestCriteria($request));
        $airlines = $this->airlineRepo->orderby('name', 'asc')->get();

        return view('admin.airlines.index', [
            'airlines' => $airlines,
        ]);
    }

    /**
     * Show the form for creating a new Airlines.
     */
    public function create(): View
    {
        return view('admin.airlines.create', [
            'countries' => Countries::getSelectList(),
        ]);
    }

    /**
     * Store a newly created Airlines in storage.
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateAirlineRequest $request): RedirectResponse
    {
        $input = $request->all();
        $this->airlineSvc->createAirline($input);

        Flash::success('Airlines saved successfully.');
        return redirect(route('admin.airlines.index'));
    }

    /**
     * Display the specified Airlines.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function show(int $id): View
    {
        $airlines = $this->airlineRepo->findWithoutFail($id);

        if (empty($airlines)) {
            Flash::error('Airlines not found');
            return redirect(route('admin.airlines.index'));
        }

        return view('admin.airlines.show', [
            'airlines' => $airlines,
        ]);
    }

    /**
     * Show the form for editing the specified Airlines.
     *
     * @param int $id
     *
     * @return View
     */
    public function edit(int $id): View
    {
        $airline = $this->airlineRepo->findWithoutFail($id);

        if (empty($airline)) {
            Flash::error('Airline not found');
            return redirect(route('admin.airlines.index'));
        }

        return view('admin.airlines.edit', [
            'airline'   => $airline,
            'countries' => Countries::getSelectList(),
        ]);
    }

    /**
     * Update the specified Airlines in storage.
     *
     * @param int                  $id
     * @param UpdateAirlineRequest $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return RedirectResponse
     */
    public function update(int $id, UpdateAirlineRequest $request): RedirectResponse
    {
        $airlines = $this->airlineRepo->findWithoutFail($id);

        if (empty($airlines)) {
            Flash::error('Airlines not found');
            return redirect(route('admin.airlines.index'));
        }

        $airlines = $this->airlineRepo->update($request->all(), $id);

        Flash::success('Airlines updated successfully.');
        return redirect(route('admin.airlines.index'));
    }

    /**
     * Remove the specified Airlines from storage.
     *
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $airline = $this->airlineRepo->findWithoutFail($id);

        if (empty($airline)) {
            Flash::error('Airlines not found');
            return redirect(route('admin.airlines.index'));
        }

        if (!$this->airlineSvc->canDeleteAirline($airline)) {
            Flash::error('Airlines cannot be deleted; flights/PIREPs/subfleets exist');
            return redirect(route('admin.airlines.index'));
        }

        $this->airlineRepo->delete($id);

        Flash::success('Airlines deleted successfully.');
        return redirect(route('admin.airlines.index'));
    }
}
