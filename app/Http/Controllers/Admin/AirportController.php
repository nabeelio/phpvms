<?php

namespace App\Http\Controllers\Admin;

use Log;
use Flash;
use App\Http\Requests\CreateAirportRequest;
use App\Http\Requests\UpdateAirportRequest;
use App\Repositories\AirportRepository;
use App\Repositories\Criteria\WhereCriteria;
use Illuminate\Http\Request;
use Jackiedo\Timezonelist\Facades\Timezonelist;
use Response;


class AirportController extends BaseController
{
    /** @var  AirportRepository */
    private $airportRepository;

    public static $enabledStates = [
        'on',
        'true',
        '1',
        true,
    ];

    public function __construct(AirportRepository $airportRepo)
    {
        $this->airportRepository = $airportRepo;
    }

    /**
     * Display a listing of the Airport.
     *
     * @param Request $request
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {
        $where = [];
        if($request->has('icao')) {
            $where['icao'] = $request->get('icao');
        }

        $this->airportRepository->pushCriteria(new WhereCriteria($request, $where));
        $airports = $this->airportRepository->orderBy('icao', 'asc')->paginate(40);

        return view('admin.airports.index', [
            'airports' => $airports,
        ]);
    }

    /**
     * Show the form for creating a new Airport.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.airports.create', [
            'timezones' => Timezonelist::toArray(),
        ]);
    }

    /**
     * Store a newly created Airport in storage.
     * @param CreateAirportRequest $request
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateAirportRequest $request)
    {
        $input = $request->all();
        $input['hub'] = \in_array($input['hub'], self::$enabledStates);

        $this->airportRepository->create($input);

        Flash::success('Airport saved successfully.');
        return redirect(route('admin.airports.index'));
    }

    /**
     * Display the specified Airport.
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $airport = $this->airportRepository->findWithoutFail($id);

        if (empty($airport)) {
            Flash::error('Airport not found');
            return redirect(route('admin.airports.index'));
        }

        return view('admin.airports.show', [
            'airport' => $airport,
        ]);
    }

    /**
     * Show the form for editing the specified Airport.
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $airport = $this->airportRepository->findWithoutFail($id);

        if (empty($airport)) {
            Flash::error('Airport not found');
            return redirect(route('admin.airports.index'));
        }

        return view('admin.airports.edit', [
            'timezones' => Timezonelist::toArray(),
            'airport' => $airport,
        ]);
    }

    /**
     * Update the specified Airport in storage.
     * @param  int $id
     * @param UpdateAirportRequest $request
     * @return Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update($id, UpdateAirportRequest $request)
    {
        $airport = $this->airportRepository->findWithoutFail($id);

        if (empty($airport)) {
            Flash::error('Airport not found');
            return redirect(route('admin.airports.index'));
        }

        $attrs = $request->all();
        $attrs['hub'] = \in_array($attrs['hub'], self::$enabledStates);

        $this->airportRepository->update($attrs, $id);

        Flash::success('Airport updated successfully.');
        return redirect(route('admin.airports.index'));
    }

    /**
     * Remove the specified Airport from storage.
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $airport = $this->airportRepository->findWithoutFail($id);

        if (empty($airport)) {
            Flash::error('Airport not found');
            return redirect(route('admin.airports.index'));
        }

        $this->airportRepository->delete($id);

        Flash::success('Airport deleted successfully.');
        return redirect(route('admin.airports.index'));
    }

    public function fuel(Request $request)
    {
        $id = $request->id;

        $airport = $this->airportRepository->findWithoutFail($id);
        if (empty($airport)) {
            Flash::error('Flight not found');
            return redirect(route('admin.flights.index'));
        }

        // add aircraft to flight
        if ($request->isMethod('put')) {
            $airport->{$request->name} = $request->value;
        }

        $airport->save();
    }
}
