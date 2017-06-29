<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreatePirepRequest;
use App\Http\Requests\UpdatePirepRequest;
use App\Repositories\PirepRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PirepController extends BaseController
{
    private $pirepRepository;

    public function __construct(PirepRepository $pirepRepo)
    {
        $this->pirepRepository = $pirepRepo;
    }

    /**
     * Display a listing of the Pirep.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->pirepRepository->pushCriteria(new RequestCriteria($request));
        $pireps = $this->pirepRepository->all();

        return view('admin.pireps.index', [
            'pireps' => $pireps
        ]);
    }

    /**
     * Show the form for creating a new Pirep.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.pireps.create');
    }

    /**
     * Store a newly created Pirep in storage.
     *
     * @param CreatePirepRequest $request
     *
     * @return Response
     */
    public function store(CreatePirepRequest $request)
    {
        $input = $request->all();
        $pirep = $this->pirepRepository->create($input);

        Flash::success('Pirep saved successfully.');
        return redirect(route('admin.pireps.index'));
    }

    /**
     * Display the specified Pirep.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $pirep = $this->pirepRepository->findWithoutFail($id);

        if (empty($pirep)) {
            Flash::error('Pirep not found');
            return redirect(route('admin.pireps.index'));
        }

        return view('admin.pireps.show', [
            'pirep' => $pirep,
        ]);
    }

    /**
     * Show the form for editing the specified Pirep.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $pirep = $this->pirepRepository->findWithoutFail($id);

        if (empty($pirep)) {
            Flash::error('Pirep not found');
            return redirect(route('admin.pireps.index'));
        }

        return view('admin.pireps.edit', [
            'pirep' => $pirep,
        ]);
    }

    /**
     * Update the specified Pirep in storage.
     *
     * @param  int              $id
     * @param UpdatePirepRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePirepRequest $request)
    {
        $pirep = $this->pirepRepository->findWithoutFail($id);

        if (empty($pirep)) {
            Flash::error('Pirep not found');
            return redirect(route('admin.pireps.index'));
        }

        $pirep = $this->pirepRepository->update($request->all(), $id);

        Flash::success('Pirep updated successfully.');
        return redirect(route('admin.pireps.index'));
    }

    /**
     * Remove the specified Pirep from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $pirep = $this->pirepRepository->findWithoutFail($id);

        if (empty($pirep)) {
            Flash::error('Pirep not found');
            return redirect(route('admin.pireps.index'));
        }

        $this->pirepRepository->delete($id);

        Flash::success('Pirep deleted successfully.');
        return redirect(route('admin.pireps.index'));
    }
}
