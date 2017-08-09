@foreach($pireps as $pirep)
<div class="card">
    <div class="card-block" style="min-height: 0px">
        <div class="row">
            <div class="col-sm-2">
                <h5>
                    <a class="text-c" href="{!! route('frontend.flights.show', [$pirep->flight_id]) !!}">
                        {!! $pirep->airline->code !!}{!! $pirep->flight->flight_number !!}
                    </a>
                </h5>
                <div>
                    @if($pirep->status == config('enums.pirep_status.PENDING'))
                        <div class="badge badge-warning">Pending</div>
                    @elseif($pirep->status == config('enums.pirep_status.ACCEPTED'))
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
                            {!! Utils::secondsToTime($pirep->flight_time) !!}
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
@endforeach
