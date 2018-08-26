<?php

namespace App\Http\Controllers\Admin;

use App\Interfaces\Controller;
use App\Repositories\FlightFieldRepository;
use Flash;
use Illuminate\Http\Request;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FlightFieldController
 */
class FlightFieldController extends Controller
{
    private $flightFieldRepo;

    /**
     * FlightFieldController constructor.
     *
     * @param FlightFieldRepository $flightFieldRepository
     */
    public function __construct(
        FlightFieldRepository $flightFieldRepository
    ) {
        $this->flightFieldRepo = $flightFieldRepository;
    }

    /**
     * Display a listing of the FlightField.
     *
     * @param Request $request
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $this->flightFieldRepo->pushCriteria(new RequestCriteria($request));
        $fields = $this->flightFieldRepo->all();

        return view('admin.flightfields.index', [
            'fields' => $fields,
        ]);
    }

    /**
     * Show the form for creating a new FlightField.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.flightfields.create');
    }

    /**
     * Store a newly created FlightField in storage.
     *
     * @param Request $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $attrs = $request->all();
        $attrs['slug'] = str_slug($attrs['name']);

        $this->flightFieldRepo->create($attrs);

        Flash::success('Field added successfully.');
        return redirect(route('admin.flightfields.index'));
    }

    /**
     * Display the specified FlightField.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $field = $this->flightFieldRepo->findWithoutFail($id);

        if (empty($field)) {
            Flash::error('Flight field not found');
            return redirect(route('admin.flightfields.index'));
        }

        return view('admin.flightfields.show', [
            'field' => $field,
        ]);
    }

    /**
     * Show the form for editing the specified FlightField.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $field = $this->flightFieldRepo->findWithoutFail($id);

        if (empty($field)) {
            Flash::error('Field not found');
            return redirect(route('admin.flightfields.index'));
        }

        return view('admin.flightfields.edit', [
            'field' => $field,
        ]);
    }

    /**
     * Update the specified FlightField in storage.
     *
     * @param         $id
     * @param Request $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update($id, Request $request)
    {
        $field = $this->flightFieldRepo->findWithoutFail($id);

        if (empty($field)) {
            Flash::error('FlightField not found');
            return redirect(route('admin.flightfields.index'));
        }

        $attrs = $request->all();
        $attrs['slug'] = str_slug($attrs['name']);
        $this->flightFieldRepo->update($attrs, $id);

        Flash::success('Field updated successfully.');
        return redirect(route('admin.flightfields.index'));
    }

    /**
     * Remove the specified FlightField from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $field = $this->flightFieldRepo->findWithoutFail($id);

        if (empty($field)) {
            Flash::error('Field not found');
            return redirect(route('admin.flightfields.index'));
        }

        $this->flightFieldRepo->delete($id);

        Flash::success('Field deleted successfully.');
        return redirect(route('admin.flightfields.index'));
    }
}
