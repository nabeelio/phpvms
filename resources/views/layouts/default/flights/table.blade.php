@foreach($flights as $flight)
<div class="card">
    <div class="card-block" style="min-height: 0px">
        <div class="row">
            <div class="col-sm-1">
                <a href="{!! route('frontend.flights.show', [$flight->id]) !!}">
                    {!! $flight->airline->code !!}{!! $flight->flight_number !!}
                    @if($flight->route_code)
                        (C: {!! $flight->route_code !!} L: {!! $flight->route_leg !!})
                    @endif
                </a>
            </div>
            <div class="col-sm-11">
                <div class="row">
                    <div class="col-sm-4">
                            <span class="title">DEP&nbsp;</span>
                            {!! $flight->dpt_airport->icao !!}&nbsp;
                            <span class="description">{!! $flight->dpt_time !!}</span>
                    </div>
                    <div class="col-sm-4">
                            <span class="title">ARR&nbsp;</span>
                            {!! $flight->arr_airport->icao !!}&nbsp;
                            <span class="description">{!! $flight->arr_time !!}</span>

                            @if($flight->alt_airport)
                                <span class="description">Alt: {!! $flight->alt_airport->icao !!}
                                    )</span>
                            @endif
                    </div>
                    <div class="col-sm-4">
                    </div>
                    <div class="col-sm-12">
                        <span class="description">ROUTE&nbsp;</span>
                        {!! $flight->route !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endforeach
