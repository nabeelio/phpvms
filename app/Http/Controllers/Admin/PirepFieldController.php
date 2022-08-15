<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Http\Requests\CreatePirepFieldRequest;
use App\Http\Requests\UpdatePirepFieldRequest;
use App\Repositories\PirepFieldRepository;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Prettus\Repository\Criteria\RequestCriteria;

class PirepFieldController extends Controller
{
    private PirepFieldRepository $pirepFieldRepo;

    /**
     * PirepFieldController constructor.
     *
     * @param PirepFieldRepository $pirepFieldRepo
     */
    public function __construct(
        PirepFieldRepository $pirepFieldRepo
    ) {
        $this->pirepFieldRepo = $pirepFieldRepo;
    }

    /**
     * Display a listing of the PirepField.
     *
     * @param Request $request
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     *
     * @return mixed
     */
    public function index(Request $request)
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
     * @return mixed
     */
    public function create()
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
     * @return mixed
     */
    public function store(CreatePirepFieldRequest $request)
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
     * @return mixed
     */
    public function show($id)
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
     * @return mixed
     */
    public function edit($id)
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
     * @param mixed $id
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update($id, UpdatePirepFieldRequest $request)
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
     * @return mixed
     */
    public function destroy($id)
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
