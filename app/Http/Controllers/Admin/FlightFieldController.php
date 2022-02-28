<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Repositories\FlightFieldRepository;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Prettus\Repository\Criteria\RequestCriteria;

class FlightFieldController extends Controller
{
    private FlightFieldRepository $flightFieldRepo;

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
     * @return mixed
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
     * @return mixed
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
     * @return mixed
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
     * @return mixed
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
     * @return mixed
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
     * @return mixed
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
