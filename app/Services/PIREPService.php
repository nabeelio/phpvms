<?php

namespace App\Services;

use App\Models\Pirep;
use App\Models\PirepFieldValues;

use App\Repositories\PirepRepository;
use App\Repositories\SubfleetRepository;


class PIREPService extends BaseService {

    protected $aircraftRepo, $pirepRepo;

    /**
     * return a PIREP model
     */
    public function __construct(
        SubfleetRepository $aircraftRepo,
        PirepRepository $pirepRepo
    ) {
        $this->aircraftRepo = $aircraftRepo;
        $this->pirepRepo = $pirepRepo;
    }

    public function create(
        Pirep $pirep,
        array $field_values  # PirepFieldValues
    ) {

        $pirep->save();

        foreach($field_values as $fv) {
            $v = new PirepFieldValues();
            $v->name = $fv['name'];
            $v->value = $fv['value'];
            $v->source = $fv['source'];
            $v->save();
        }

        # TODO: Financials
    }
}
