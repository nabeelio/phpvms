<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Http\Requests\CreatePirepFieldRequest;
use App\Http\Requests\UpdatePirepFieldRequest;
use App\Repositories\PirepFieldRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laracasts\Flash\Flash;
use Prettus\Repository\Criteria\RequestCriteria;

class PirepFieldController extends Controller
{
    /**
     * PirepFieldController constructor.
     *
     * @param PirepFieldRepository $pirepFieldRepo
     */
    public function __construct(
        private readonly PirepFieldRepository $pirepFieldRepo
    ) {
    }

    /**
     * Display a listing of the PirepField.
     *
     * @param Request $request
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     *
     * @return View
     */
    public function index(Request $request): View
    {
        $this->pirepFieldRepo->pushCriteria(new RequestCriteria($request));
        $fields = $this->pirepFieldRepo->all();

        return view('admin.pirepfields.index', [
            'fields' => $fields,
        ]);
    }

    /**
     * Show the form for creating a new PirepField.
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.pirepfields.create');
    }

    /**
     * Store a newly created PirepField in storage.
     *
     * @param CreatePirepFieldRequest $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return RedirectResponse
     */
    public function store(CreatePirepFieldRequest $request): RedirectResponse
    {
        $attrs = $request->all();
        $attrs['slug'] = str_slug($attrs['name']);
        $attrs['required'] = get_truth_state($attrs['required']);

        $this->pirepFieldRepo->create($attrs);

        Flash::success('Field added successfully.');

        return redirect(route('admin.pirepfields.index'));
    }

    /**
     * Display the specified PirepField.
     *
     * @param int $id
     *
     * @return RedirectResponse|View
     */
    public function show(int $id): RedirectResponse|View
    {
        $field = $this->pirepFieldRepo->findWithoutFail($id);

        if (empty($field)) {
            Flash::error('PirepField not found');

            return redirect(route('admin.pirepfields.index'));
        }

        return view('admin.pirepfields.show', [
            'field' => $field,
        ]);
    }

    /**
     * Show the form for editing the specified PirepField.
     *
     * @param int $id
     *
     * @return RedirectResponse|View
     */
    public function edit(int $id): RedirectResponse|View
    {
        $field = $this->pirepFieldRepo->findWithoutFail($id);

        if (empty($field)) {
            Flash::error('Field not found');

            return redirect(route('admin.pirepfields.index'));
        }

        return view('admin.pirepfields.edit', [
            'field' => $field,
        ]);
    }

    /**
     * Update the specified PirepField in storage.
     *
     * @param int $id
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return RedirectResponse
     */
    public function update(int $id, UpdatePirepFieldRequest $request): RedirectResponse
    {
        $field = $this->pirepFieldRepo->findWithoutFail($id);

        if (empty($field)) {
            Flash::error('PirepField not found');

            return redirect(route('admin.pirepfields.index'));
        }

        $attrs = $request->all();
        $attrs['slug'] = str_slug($attrs['name']);
        $attrs['required'] = get_truth_state($attrs['required']);
        $this->pirepFieldRepo->update($attrs, $id);

        Flash::success('Field updated successfully.');

        return redirect(route('admin.pirepfields.index'));
    }

    /**
     * Remove the specified PirepField from storage.
     *
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $field = $this->pirepFieldRepo->findWithoutFail($id);

        if (empty($field)) {
            Flash::error('Field not found');

            return redirect(route('admin.pirepfields.index'));
        }

        $this->pirepFieldRepo->delete($id);

        Flash::success('Field deleted successfully.');

        return redirect(route('admin.pirepfields.index'));
    }
}
