<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateAwardRequest;
use App\Http\Requests\UpdateAwardRequest;
use App\Contracts\Controller;
use App\Repositories\AwardRepository;
use App\Services\AwardService;
use Flash;
use Illuminate\Http\Request;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AwardController extends Controller
{
    /** @var AwardRepository */
    private $awardRepository;
    private $awardSvc;

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
     * @return Response
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
     *
     * @return Response
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
     * @return Response
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
     *
     * @param int $id
     *
     * @return Response
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
     * @return Response
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
