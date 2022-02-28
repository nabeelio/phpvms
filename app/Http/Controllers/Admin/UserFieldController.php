<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Repositories\UserFieldRepository;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Prettus\Repository\Criteria\RequestCriteria;

class UserFieldController extends Controller
{
    /** @var \App\Repositories\UserFieldRepository */
    private UserFieldRepository $userFieldRepo;

    /**
     * @param UserFieldRepository $userFieldRepo
     */
    public function __construct(UserFieldRepository $userFieldRepo)
    {
        $this->userFieldRepo = $userFieldRepo;
    }

    /**
     * Display a listing of the UserField.
     *
     * @param Request $request
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $this->userFieldRepo->pushCriteria(new RequestCriteria($request));
        $fields = $this->userFieldRepo->all();

        return view('admin.userfields.index', ['fields' => $fields]);
    }

    /**
     * Show the form for creating a new UserField.
     */
    public function create()
    {
        return view('admin.userfields.create');
    }

    /**
     * Store a newly created UserField in storage.
     *
     * @param Request $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        $this->userFieldRepo->create($request->all());

        Flash::success('Field added successfully.');
        return redirect(route('admin.userfields.index'));
    }

    /**
     * Display the specified UserField.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function show($id)
    {
        $field = $this->userFieldRepo->findWithoutFail($id);

        if (empty($field)) {
            Flash::error('Flight field not found');
            return redirect(route('admin.userfields.index'));
        }

        return view('admin.userfields.show', ['field' => $field]);
    }

    /**
     * Show the form for editing the specified UserField.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function edit($id)
    {
        $field = $this->userFieldRepo->findWithoutFail($id);

        if (empty($field)) {
            Flash::error('Field not found');
            return redirect(route('admin.userfields.index'));
        }

        return view('admin.userfields.edit', ['field' => $field]);
    }

    /**
     * Update the specified UserField in storage.
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
        $field = $this->userFieldRepo->findWithoutFail($id);

        if (empty($field)) {
            Flash::error('UserField not found');
            return redirect(route('admin.userfields.index'));
        }

        $this->userFieldRepo->update($request->all(), $id);

        Flash::success('Field updated successfully.');
        return redirect(route('admin.userfields.index'));
    }

    /**
     * Remove the specified UserField from storage.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function destroy($id)
    {
        $field = $this->userFieldRepo->findWithoutFail($id);
        if (empty($field)) {
            Flash::error('Field not found');
            return redirect(route('admin.userfields.index'));
        }

        if ($this->userFieldRepo->isInUse($id)) {
            Flash::error('This field cannot be deleted, it is in use. Deactivate it instead');
            return redirect(route('admin.userfields.index'));
        }

        $this->userFieldRepo->delete($id);

        Flash::success('Field deleted successfully.');
        return redirect(route('admin.userfields.index'));
    }
}
