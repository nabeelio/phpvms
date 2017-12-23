<div class="card border-blue-bottom">
    <div class="card-block" style="min-height: 0px">
        <div class="row">
            <div class="col-sm-2 text-center">
                <h5>
                    <a class="text-c" href="{!! route('frontend.pireps.show', [$pirep->id]) !!}">
                        {!! $pirep->airline->code !!}
                        @if($pirep->flight_id)
                            {!! $pirep->flight->flight_number !!}
                        @else
                            {!! $pirep->flight_number !!}
                        @endif
                    </a>
                </h5>
                <div>
                    @if($pirep->state == PirepState::PENDING)
                        <div class="badge badge-warning">Pending</div>
                    @elseif($pirep->state == PirepState::ACCEPTED)
                        <div class="badge badge-success">Accepted</div>
                    @else
                        <div class="badge badge-danger">Rejected</div>
                    @endif
                </div>
            </div>
            <div class="col-sm-10">
                <div class="row">
                    <div class="col-sm-4">
                        <div>
                            <span class="description">DEP&nbsp;</span>
                            {!! $pirep->dpt_airport->icao !!}&nbsp;
                            <span class="description">ARR&nbsp;</span>
                            {!! $pirep->arr_airport->icao !!}&nbsp;
                        </div>
                        <div><span class="description">Flight Time&nbsp;</span>
                            {!! Utils::minutesToTimeString($pirep->flight_time) !!}
                        </div>
                        <div><span class="description">Aircraft&nbsp;</span>
                            {!! $pirep->aircraft->registration !!} ({!! $pirep->aircraft->name !!})
                        </div>
                        <div>
                            <span class="description">Flight Level&nbsp;</span>
                            {!! $pirep->level !!}
                        </div>
                        <div>
                            <span class="description">File Date&nbsp;</span>
                            {!! $pirep->created_at !!}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <span class="description">more data&nbsp;</span>
                    </div>
                    <div class="col-sm-4">
                        <span class="description">more data&nbsp;</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
