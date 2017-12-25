<?php

namespace App\Http\Controllers\Api;

use Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Acars;
use App\Models\Enums\PirepState;
use App\Models\Enums\PirepStatus;
use App\Http\Resources\Acars as AcarsResource;
use App\Http\Resources\Pirep as PirepResource;


use App\Repositories\AcarsRepository;
use App\Repositories\PirepRepository;

use App\Http\Controllers\AppBaseController;

class PirepController extends AppBaseController
{
    protected $acarsRepo, $pirepRepo;

    public function __construct(
        AcarsRepository $acarsRepo,
        PirepRepository $pirepRepo
    ) {
        $this->acarsRepo = $acarsRepo;
        $this->pirepRepo = $pirepRepo;
    }

    public function get($id)
    {
        PirepResource::withoutWrapping();
        return new PirepResource($this->pirepRepo->find($id));
    }

    /**
     * Create a new PIREP and place it in a "inprogress" and "prefile" state
     * Once ACARS updates are being processed, then it can go into an 'ENROUTE'
     * status, and whatever other statuses may be defined
     *
     * TODO: Allow extra fields, etc to be set. Aircraft, etc
     */
    public function prefile(Request $request)
    {
        Log::info('PIREP Prefile, user '. Auth::user()->pilot_id,
                  $request->toArray());

        /*$validator = Validator::make($request, [
            'aircraft_id'       => 'required',
            'dpt_airport_id'    => 'required',
            'arr_airport_id'    => 'required',
            'altitude'          => 'nullable|integer',
            'route'             => 'nullable',
            'notes'             => 'nullable',
        ]);*/

        $attr = [];
        $attr['user_id'] = Auth::user()->id;
        $attr['airline_id'] = $request->get('airline_id');
        $attr['aircraft_id'] = $request->get('aircraft_id');
        $attr['dpt_airport_id'] = $request->get('dpt_airport');
        $attr['arr_airport_id'] = $request->get('arr_airport');
        $attr['altitude'] = $request->get('altitude');
        $attr['route'] = $request->get('route');
        $attr['notes'] = $request->get('notes');
        $attr['state'] = PirepState::IN_PROGRESS;
        $attr['status'] = PirepStatus::PREFILE;

        try {
            $pirep = $this->pirepRepo->create($attr);
        } catch(\Exception $e) {
            Log::error($e);
        }

        Log::info('PIREP PREFILED');
        Log::info($pirep->id);

        PirepResource::withoutWrapping();
        return new PirepResource($pirep);
    }

    /**
     * Get all of the ACARS updates for a PIREP
     * @param $id
     * @return AcarsResource
     */
    public function acars_get($id)
    {
        $pirep = $this->pirepRepo->find($id);

        $updates = $this->acarsRepo->forPirep($id);
        return new AcarsResource($updates);
    }

    /**
     * Post ACARS updates for a PIREP
     * @param $id
     * @param Request $request
     * @return AcarsResource
     */
    public function acars_store($id, Request $request)
    {
        $pirep = $this->pirepRepo->find($id);

        Log::info('Posting ACARS update', $request->toArray());
        $attrs = $request->toArray();
        $attrs['pirep_id'] = $id;

        $update = Acars::create($attrs);
        $update->save();

        # Change the PIREP status
        $pirep->status = PirepStatus::ENROUTE;
        $pirep->save();

        AcarsResource::withoutWrapping();
        return new AcarsResource($update);
    }
}
