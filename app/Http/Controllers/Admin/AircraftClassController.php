<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateAircraftClassRequest;
use App\Http\Requests\UpdateAircraftClassRequest;
use App\Repositories\AircraftClassRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AircraftClassController extends BaseController
{
    /** @var  AircraftClassRepository */
    private $aircraftClassRepository;

    public function __construct(AircraftClassRepository $aircraftClassRepo)
    {
        $this->aircraftClassRepository = $aircraftClassRepo;
    }

    /**
     * Display a listing of the AircraftClass.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->aircraftClassRepository->pushCriteria(new RequestCriteria($request));
        $aircraftClasses = $this->aircraftClassRepository->all();

        return view('admin.aircraft_classes.index')
                ->with('aircraftClasses', $aircraftClasses);
    }

    /**
     * Show the form for creating a new AircraftClass.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.aircraft_classes.create');
    }

    /**
     * Store a newly created AircraftClass in storage.
     *
     * @param CreateAircraftClassRequest $request
     *
     * @return Response
     */
    public function store(CreateAircraftClassRequest $request)
    {
        $input = $request->all();
        $aircraftClass = $this->aircraftClassRepository->create($input);

        Flash::success('Aircraft Class saved successfully.');

        return redirect(route('admin.aircraftClasses.index'));
    }

    /**
     * Display the specified AircraftClass.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $aircraftClass = $this->aircraftClassRepository->findWithoutFail($id);

        if (empty($aircraftClass)) {
            Flash::error('Aircraft Class not found');
            return redirect(route('admin.aircraftClasses.index'));
        }

        return view('admin.aircraft_classes.show')->with('aircraftClass', $aircraftClass);
    }

    /**
     * Show the form for editing the specified AircraftClass.
     *
     * @param  int $id
     *
     * @return mixed
     */
    public function edit($id)
    {
        $aircraftClass = $this->aircraftClassRepository->findWithoutFail($id);

        if (empty($aircraftClass)) {
            Flash::error('Aircraft Class not found');
            return redirect(route('admin.aircraftClasses.index'));
        }

        return view('admin.aircraft_classes.edit')->with('aircraftClass', $aircraftClass);
    }

    /**
     * Update the specified AircraftClass in storage.
     *
     * @param  int              $id
     * @param UpdateAircraftClassRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAircraftClassRequest $request)
    {
        $aircraftClass = $this->aircraftClassRepository->findWithoutFail($id);

        if (empty($aircraftClass)) {
            Flash::error('Aircraft Class not found');
            return redirect(route('admin.aircraftClasses.index'));
        }

        $aircraftClass = $this->aircraftClassRepository->update($request->all(), $id);

        Flash::success('Aircraft Class updated successfully.');

        return redirect(route('admin.aircraftClasses.index'));
    }

    /**
     * Remove the specified AircraftClass from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $aircraftClass = $this->aircraftClassRepository->findWithoutFail($id);

        if (empty($aircraftClass)) {
            Flash::error('Aircraft Class not found');
            return redirect(route('admin.aircraftClasses.index'));
        }

        $this->aircraftClassRepository->delete($id);

        Flash::success('Aircraft Class deleted successfully.');

        return redirect(route('admin.aircraftClasses.index'));
    }
}
