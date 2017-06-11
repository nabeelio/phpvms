<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateFareRequest;
use App\Http\Requests\UpdateFareRequest;
use App\Repositories\FareRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class FareController extends BaseController
{
    /** @var  FareRepository */
    private $fareRepository;

    public function __construct(FareRepository $fareRepo)
    {
        $this->fareRepository = $fareRepo;
    }

    /**
     * Display a listing of the Fare.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $this->fareRepository->pushCriteria(new RequestCriteria($request));
        $fares = $this->fareRepository->all();

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
     *
     * @param CreateFareRequest $request
     *
     * @return Response
     */
    public function store(CreateFareRequest $request)
    {
        $input = $request->all();
        $fare = $this->fareRepository->create($input);
        Flash::success('Fare saved successfully.');

        return redirect(route('admin.fares.index'));
    }

    /**
     * Display the specified Fare.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $fare = $this->fareRepository->findWithoutFail($id);
        if (empty($fare)) {
            Flash::error('Fare not found');
            return redirect(route('admin.fares.index'));
        }

        return view('admin.fares.show')->with('fare', $fare);
    }

    /**
     * Show the form for editing the specified Fare.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $fare = $this->fareRepository->findWithoutFail($id);
        if (empty($fare)) {
            Flash::error('Fare not found');
            return redirect(route('admin.fares.index'));
        }

        return view('admin.fares.edit')->with('fare', $fare);
    }

    /**
     * Update the specified Fare in storage.
     *
     * @param  int              $id
     * @param UpdateFareRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFareRequest $request)
    {
        $fare = $this->fareRepository->findWithoutFail($id);
        if (empty($fare)) {
            Flash::error('Fare not found');
            return redirect(route('admin.fares.index'));
        }

        $fare = $this->fareRepository->update($request->all(), $id);
        Flash::success('Fare updated successfully.');

        return redirect(route('admin.fares.index'));
    }

    /**
     * Remove the specified Fare from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $fare = $this->fareRepository->findWithoutFail($id);
        if (empty($fare)) {
            Flash::error('Fare not found');
            return redirect(route('admin.fares.index'));
        }

        $this->fareRepository->delete($id);
        Flash::success('Fare deleted successfully.');

        return redirect(route('admin.fares.index'));
    }
}
