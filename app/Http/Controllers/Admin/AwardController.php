<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Http\Requests\CreateAwardRequest;
use App\Http\Requests\UpdateAwardRequest;
use App\Repositories\AwardRepository;
use App\Services\AwardService;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Prettus\Repository\Criteria\RequestCriteria;

class AwardController extends Controller
{
    /** @var AwardRepository */
    private AwardRepository $awardRepository;
    private AwardService $awardSvc;

    /**
     * AwardController constructor.
     *
     * @param AwardRepository $awardRepo
     * @param AwardService    $awardSvc
     */
    public function __construct(
        AwardRepository $awardRepo,
        AwardService $awardSvc
    ) {
        $this->awardRepository = $awardRepo;
        $this->awardSvc = $awardSvc;
    }

    /**
     * @return array
     */
    protected function getAwardClassesAndDescriptions(): array
    {
        $awards = [
            '' => '',
        ];

        $descriptions = [];

        $award_classes = $this->awardSvc->findAllAwardClasses();
        foreach ($award_classes as $class_ref => $award) {
            $awards[$class_ref] = $award->name;
            $descriptions[$class_ref] = $award->param_description;
        }

        return [
            'awards'       => $awards,
            'descriptions' => $descriptions,
        ];
    }

    /**
     * Display a listing of the Fare.
     *
     * @param Request $request
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     *
     * @return mixed
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
     */
    public function create()
    {
        $class_refs = $this->getAwardClassesAndDescriptions();

        return view('admin.awards.create', [
            'award_classes'      => $class_refs['awards'],
            'award_descriptions' => $class_refs['descriptions'],
        ]);
    }

    /**
     * Store a newly created Fare in storage.
     *
     * @param CreateAwardRequest $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return mixed
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
     *
     * @param int $id
     *
     * @return mixed
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
     *
     * @param int $id
     *
     * @return mixed
     */
    public function edit($id)
    {
        $award = $this->awardRepository->findWithoutFail($id);
        if (empty($award)) {
            Flash::error('Award not found');

            return redirect(route('admin.awards.index'));
        }

        $class_refs = $this->getAwardClassesAndDescriptions();

        return view('admin.awards.edit', [
            'award'              => $award,
            'award_classes'      => $class_refs['awards'],
            'award_descriptions' => $class_refs['descriptions'],
        ]);
    }

    /**
     * Update the specified award in storage.
     *
     * @param int                $id
     * @param UpdateAwardRequest $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return mixed
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
     * @param int $id
     *
     * @return mixed
     */
    public function destroy($id)
    {
        $award = $this->awardRepository->findWithoutFail($id);
        if (empty($award)) {
            Flash::error('Award not found');

            return redirect(route('admin.awards.index'));
        }

        $this->awardRepository->delete($id);
        Flash::success('Award deleted successfully.');

        return redirect(route('admin.awards.index'));
    }
}
