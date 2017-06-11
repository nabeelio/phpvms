<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateAirlinesRequest;
use App\Http\Requests\UpdateAirlinesRequest;
use App\Repositories\AirlinesRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AirlinesController extends BaseController
{
    /** @var  AirlinesRepository */
    private $airlinesRepository;

    public function __construct(AirlinesRepository $airlinesRepo)
    {
        $this->airlinesRepository = $airlinesRepo;
    }

    /**
     * Display a listing of the Airlines.
     */
    public function index(Request $request)
    {
        $this->airlinesRepository->pushCriteria(new RequestCriteria($request));
        $airlines = $this->airlinesRepository->all();

        return view('admin.airlines.index')
            ->with('airlines', $airlines);
    }

    /**
     * Show the form for creating a new Airlines.
     */
    public function create()
    {
        return view('admin.airlines.create');
    }

    /**
     * Store a newly created Airlines in storage.
     */
    public function store(CreateAirlinesRequest $request)
    {
        $input = $request->all();
        $airlines = $this->airlinesRepository->create($input);

        Flash::success('Airlines saved successfully.');

        return redirect(route('airlines.index'));
    }

    /**
     * Display the specified Airlines.
     *
     * @param  int $id
     *
     * @return mixed
     */
    public function show($id)
    {
        $airlines = $this->airlinesRepository->findWithoutFail($id);

        if (empty($airlines)) {
            Flash::error('Airlines not found');
            return redirect(route('airlines.index'));
        }

        return view('admin.airlines.show')->with('airlines', $airlines);
    }

    /**
     * Show the form for editing the specified Airlines.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $airlines = $this->airlinesRepository->findWithoutFail($id);

        if (empty($airlines)) {
            Flash::error('Airlines not found');
            return redirect(route('airlines.index'));
        }

        return view('admin.airlines.edit')->with('airlines', $airlines);
    }

    /**
     * Update the specified Airlines in storage.
     *
     * @param  int              $id
     * @param UpdateAirlinesRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAirlinesRequest $request)
    {
        $airlines = $this->airlinesRepository->findWithoutFail($id);

        if (empty($airlines)) {
            Flash::error('Airlines not found');
            return redirect(route('airlines.index'));
        }

        $airlines = $this->airlinesRepository->update($request->all(), $id);

        Flash::success('Airlines updated successfully.');

        return redirect(route('airlines.index'));
    }

    /**
     * Remove the specified Airlines from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $airlines = $this->airlinesRepository->findWithoutFail($id);

        if (empty($airlines)) {
            Flash::error('Airlines not found');
            return redirect(route('airlines.index'));
        }

        $this->airlinesRepository->delete($id);

        Flash::success('Airlines deleted successfully.');

        return redirect(route('airlines.index'));
    }
}
