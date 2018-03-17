<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateAwardRequest;
use App\Http\Requests\UpdateAwardRequest;
use App\Repositories\AwardRepository;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AwardController extends BaseController
{
    /** @var  AwardRepository */
    private $awardRepository;

    public function __construct(AwardRepository $awardRepo)
    {
        $this->awardRepository = $awardRepo;
    }

    /**
     * Display a listing of the Fare.
     * @param Request $request
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {
        $this->awardRepository->pushCriteria(new RequestCriteria($request));
        $awards = $this->awardRepository->all();

        return view('admin.awards.index', [
            'awards' => $awards,
        ]);
    }

    /**
     * Show the form for creating a new Fare.
     * @return Response
     */
    public function create()
    {
        return view('admin.awards.create');
    }

    /**
     * Store a newly created Fare in storage.
     * @param CreateAwardRequest $request
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateAwardRequest $request)
    {
        $input = $request->all();
        $award = $this->awardRepository->create($input);
        Flash::success('Award saved successfully.');

        return redirect(route('admin.awards.index'));
    }

    /**
     * Display the specified Fare.
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $award = $this->awardRepository->findWithoutFail($id);
        if (empty($award)) {
            Flash::error('Award not found');
            return redirect(route('admin.awards.index'));
        }

        return view('admin.awards.show', [
            'award' => $award,
        ]);
    }

    /**
     * Show the form for editing the specified award.
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $award = $this->awardRepository->findWithoutFail($id);
        if (empty($award)) {
            Flash::error('Award not found');
            return redirect(route('admin.awards.index'));
        }

        return view('admin.awards.edit', [
            'award' => $award,
        ]);
    }

    /**
     * Update the specified award in storage.
     * @param  int $id
     * @param UpdateAwardRequest $request
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update($id, UpdateAwardRequest $request)
    {
        $award = $this->awardRepository->findWithoutFail($id);
        if (empty($award)) {
            Flash::error('Award not found');
            return redirect(route('admin.awards.index'));
        }

        $award = $this->awardRepository->update($request->all(), $id);
        Flash::success('Award updated successfully.');

        return redirect(route('admin.awards.index'));
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
        $award = $this->awardRepository->findWithoutFail($id);
        if (empty($award)) {
            Flash::error('Fare not found');
            return redirect(route('admin.awards.index'));
        }

        $this->awardRepository->delete($id);
        Flash::success('Fare deleted successfully.');

        return redirect(route('admin.awards.index'));
    }
}
