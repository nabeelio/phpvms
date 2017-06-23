<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Models\Airline;
use App\Http\Requests\CreateSubfleetRequest;
use App\Http\Requests\UpdateSubfleetRequest;
use App\Repositories\SubfleetRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class SubfleetController extends BaseController
{
    /** @var  SubfleetRepository */
    private $subfleetRepo;

    public function __construct(SubfleetRepository $subfleetRepo)
    {
        $this->subfleetRepo = $subfleetRepo;
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

        return view('admin.subfleets.show', ['subfleet' => $subfleet]);
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

        return view('admin.subfleets.edit', [
            'airlines' => Airline::all()->pluck('name', 'id'),
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
}
