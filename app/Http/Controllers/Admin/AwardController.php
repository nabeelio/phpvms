<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Http\Requests\CreateAwardRequest;
use App\Http\Requests\UpdateAwardRequest;
use App\Repositories\AwardRepository;
use App\Services\AwardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laracasts\Flash\Flash;
use Prettus\Repository\Criteria\RequestCriteria;

class AwardController extends Controller
{
    /**
     * AwardController constructor.
     *
     * @param AwardRepository $awardRepo
     * @param AwardService    $awardSvc
     */
    public function __construct(
        private readonly AwardRepository $awardRepo,
        private readonly AwardService $awardSvc
    ) {
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
     * @return View
     */
    public function index(Request $request): View
    {
        $this->awardRepo->pushCriteria(new RequestCriteria($request));
        $awards = $this->awardRepo->all();

        return view('admin.awards.index', [
            'awards' => $awards,
        ]);
    }

    /**
     * Show the form for creating a new Fare.
     */
    public function create(): View
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
     * @return RedirectResponse
     */
    public function store(CreateAwardRequest $request): RedirectResponse
    {
        $input = $request->all();
        $award = $this->awardRepo->create($input);
        Flash::success('Award saved successfully.');

        return redirect(route('admin.awards.index'));
    }

    /**
     * Display the specified Fare.
     *
     * @param int $id
     *
     * @return View
     */
    public function show(int $id): View
    {
        $award = $this->awardRepo->findWithoutFail($id);
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
     * @return RedirectResponse|View
     */
    public function edit(int $id): RedirectResponse|View
    {
        $award = $this->awardRepo->findWithoutFail($id);
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
     * @return RedirectResponse
     */
    public function update(int $id, UpdateAwardRequest $request): RedirectResponse
    {
        $award = $this->awardRepo->findWithoutFail($id);
        if (empty($award)) {
            Flash::error('Award not found');

            return redirect(route('admin.awards.index'));
        }

        $award = $this->awardRepo->update($request->all(), $id);
        Flash::success('Award updated successfully.');

        return redirect(route('admin.awards.index'));
    }

    /**
     * Remove the specified Fare from storage.
     *
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $award = $this->awardRepo->findWithoutFail($id);
        if (empty($award)) {
            Flash::error('Award not found');

            return redirect(route('admin.awards.index'));
        }

        $this->awardRepo->delete($id);
        Flash::success('Award deleted successfully.');

        return redirect(route('admin.awards.index'));
    }
}
