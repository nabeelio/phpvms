<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Repositories\FlightFieldRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laracasts\Flash\Flash;
use Prettus\Repository\Criteria\RequestCriteria;

class FlightFieldController extends Controller
{
    /**
     * FlightFieldController constructor.
     *
     * @param FlightFieldRepository $flightFieldRepo
     */
    public function __construct(
        private readonly FlightFieldRepository $flightFieldRepo
    ) {
    }

    /**
     * Display a listing of the FlightField.
     *
     * @param Request $request
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     *
     * @return View
     */
    public function index(Request $request): View
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
    public function create(): View
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
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
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
     * @return RedirectResponse|View
     */
    public function show(int $id): RedirectResponse|View
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
     * @return RedirectResponse|View
     */
    public function edit(int $id): RedirectResponse|View
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
     * @param int     $id
     * @param Request $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return RedirectResponse
     */
    public function update(int $id, Request $request): RedirectResponse
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
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
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
