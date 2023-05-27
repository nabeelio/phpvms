<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Http\Requests\CreateTypeRatingRequest;
use App\Http\Requests\UpdateTypeRatingRequest;
use App\Models\Typerating;
use App\Repositories\SubfleetRepository;
use App\Repositories\TypeRatingRepository;
use App\Services\FleetService;
use Cache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laracasts\Flash\Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

class TypeRatingController extends Controller
{
    /**
     * @param FleetService         $fleetSvc
     * @param SubfleetRepository   $subfleetRepo
     * @param TypeRatingRepository $typeratingRepo
     */
    public function __construct(
        private readonly FleetService $fleetSvc,
        private readonly SubfleetRepository $subfleetRepo,
        private readonly TypeRatingRepository $typeratingRepo
    ) {
    }

    /**
     * @param Request $request
     *
     * @throws RepositoryException
     *
     * @return View
     */
    public function index(Request $request): View
    {
        $this->typeratingRepo->pushCriteria(new RequestCriteria($request));
        $typeratings = $this->typeratingRepo->orderby('type', 'asc')->get();

        return view('admin.typeratings.index', [
            'typeratings' => $typeratings,
        ]);
    }

    /**
     * @return View
     */
    public function create(): View
    {
        return view('admin.typeratings.create');
    }

    /**
     * @param CreateTypeRatingRequest $request
     *
     * @throws ValidatorException
     *
     * @return RedirectResponse
     */
    public function store(CreateTypeRatingRequest $request): RedirectResponse
    {
        $input = $request->all();

        $model = $this->typeratingRepo->create($input);
        Flash::success('Type Rating saved successfully.');

        return redirect(route('admin.typeratings.edit', [$model->id]));
    }

    /**
     * @param int $id
     *
     * @return RedirectResponse|View
     */
    public function show(int $id): RedirectResponse|View
    {
        $typerating = $this->typeratingRepo->findWithoutFail($id);

        if (empty($typerating)) {
            Flash::error('Type Rating not found');

            return redirect(route('admin.typeratings.index'));
        }

        return view('admin.typeratings.show', [
            'typerating' => $typerating,
        ]);
    }

    /**
     * @param int $id
     *
     * @return RedirectResponse|View
     */
    public function edit(int $id): RedirectResponse|View
    {
        $typerating = $this->typeratingRepo->findWithoutFail($id);

        if (empty($typerating)) {
            Flash::error('Type Rating not found');

            return redirect(route('admin.typeratings.index'));
        }

        $avail_subfleets = $this->getAvailSubfleets($typerating);

        return view('admin.typeratings.edit', [
            'typerating'      => $typerating,
            'avail_subfleets' => $avail_subfleets,
        ]);
    }

    /**
     * @param int                     $id
     * @param UpdateTypeRatingRequest $request
     *
     * @throws ValidatorException
     *
     * @return RedirectResponse
     */
    public function update(int $id, UpdateTypeRatingRequest $request): RedirectResponse
    {
        $typerating = $this->typeratingRepo->findWithoutFail($id);

        if (empty($typerating)) {
            Flash::error('Type Rating not found');

            return redirect(route('admin.typeratings.index'));
        }

        $typerating = $this->typeratingRepo->update($request->all(), $id);
        // Cache::forget(config('cache.keys.RANKS_PILOT_LIST.key'));
        Flash::success('Type Rating updated successfully.');

        return redirect(route('admin.typeratings.index'));
    }

    /**
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $typerating = $this->typeratingRepo->findWithoutFail($id);

        if (empty($typerating)) {
            Flash::error('Type Rating not found');

            return redirect(route('admin.typeratings.index'));
        }

        $this->typeratingRepo->delete($id);

        Flash::success('Type Rating deleted successfully.');

        return redirect(route('admin.typeratings.index'));
    }

    /**
     * @param Typerating $typerating
     *
     * @return array
     */
    protected function getAvailSubfleets(Typerating $typerating): array
    {
        $retval = [];
        $all_subfleets = $this->subfleetRepo->all();
        $avail_subfleets = $all_subfleets->except($typerating->subfleets->modelKeys());
        foreach ($avail_subfleets as $subfleet) {
            $retval[$subfleet->id] = $subfleet->name.' (Airline: '.$subfleet->airline->code.')';
        }

        return $retval;
    }

    /**
     * @param Typerating $typerating
     *
     * @return View
     */
    protected function return_subfleet_view(Typerating $typerating): View
    {
        $avail_subfleets = $this->getAvailSubfleets($typerating);

        return view('admin.typeratings.subfleets', [
            'typerating'      => $typerating,
            'avail_subfleets' => $avail_subfleets,
        ]);
    }

    /**
     * @param int     $id
     * @param Request $request
     *
     * @return RedirectResponse|View
     */
    public function subfleets(int $id, Request $request): RedirectResponse|View
    {
        $typerating = $this->typeratingRepo->findWithoutFail($id);
        if (empty($typerating)) {
            Flash::error('Type Rating not found!');
            return redirect(route('admin.typeratings.index'));
        }

        // add subfleet to type rating
        if ($request->isMethod('post')) {
            $subfleet = $this->subfleetRepo->find($request->input('subfleet_id'));
            $this->fleetSvc->addSubfleetToTypeRating($subfleet, $typerating);
        }
        // remove subfleet from type rating
        elseif ($request->isMethod('delete')) {
            $subfleet = $this->subfleetRepo->find($request->input('subfleet_id'));
            $this->fleetSvc->removeSubfleetFromTypeRating($subfleet, $typerating);
        }

        return $this->return_subfleet_view($typerating);
    }
}
