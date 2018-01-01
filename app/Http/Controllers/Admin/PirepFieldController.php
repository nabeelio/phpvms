<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreatePirepFieldRequest;
use App\Http\Requests\UpdatePirepFieldRequest;
use App\Repositories\PirepFieldRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PirepFieldController extends BaseController
{
    /** @var  PirepFieldRepository */
    private $pirepFieldRepo;

    public function __construct(PirepFieldRepository $pirepFieldRepo)
    {
        $this->pirepFieldRepo = $pirepFieldRepo;
    }

    /**
     * Display a listing of the PirepField.
     *
     * @param Request $request
     * @return Response
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
     * @return Response
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
     * @return Response
     */
    public function store(CreatePirepFieldRequest $request)
    {
        $input = $request->all();

        $field = $this->pirepFieldRepo->create($input);

        Flash::success('PirepField saved successfully.');
        return redirect(route('admin.pirepfields.index'));
    }

    /**
     * Display the specified PirepField.
     *
     * @param  int $id
     *
     * @return Response
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
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $field = $this->pirepFieldRepo->findWithoutFail($id);

        if (empty($field)) {
            Flash::error('PirepField not found');
            return redirect(route('admin.pirepfields.index'));
        }

        return view('admin.pirepfields.edit', [
            'field' => $field,
        ]);
    }

    /**
     * Update the specified PirepField in storage.
     */
    public function update($id, UpdatePirepFieldRequest $request)
    {
        $field = $this->pirepFieldRepo->findWithoutFail($id);

        if (empty($field)) {
            Flash::error('PirepField not found');
            return redirect(route('admin.pirepfields.index'));
        }

        $field = $this->pirepFieldRepo->update($request->all(), $id);

        Flash::success('PirepField updated successfully.');
        return redirect(route('admin.pirepfields.index'));
    }

    /**
     * Remove the specified PirepField from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $field = $this->pirepFieldRepo->findWithoutFail($id);

        if (empty($field)) {
            Flash::error('PirepField not found');
            return redirect(route('admin.pirepfields.index'));
        }

        $this->pirepFieldRepo->delete($id);

        Flash::success('PirepField deleted successfully.');
        return redirect(route('admin.pirepfields.index'));
    }
}
