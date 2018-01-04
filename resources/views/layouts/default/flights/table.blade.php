@foreach($flights as $flight)
<div class="card border-blue-bottom">
    <div class="card-block" style="min-height: 0px">
        <div class="row">
            <div class="col-sm-9">
                <h5>
                    <a class="text-c" href="{!! route('frontend.flights.show', [$flight->id]) !!}">
                        {!! $flight->airline->code !!}{!! $flight->flight_number !!}
                        @if($flight->route_code)
                            Code: {!! $flight->route_code !!}
                        @endif
                        @if($flight->route_leg)
                            Leg: {!! $flight->route_leg !!}
                        @endif
                    </a>
                </h5>
            </div>
            <div class="col-sm-3 text-right">
                <!-- use for saved: btn-outline-primary -->
                <button class="btn btn-icon btn-icon-mini btn-round
                               {{ in_array($flight->id, $saved) ? 'btn-danger':'' }}
                               save_flight" x-id="{!! $flight->id !!}" type="button">
                    <i class="now-ui-icons ui-2_favourite-28"></i>
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-5">
                <span class="title">DEP&nbsp;</span>
                {!! $flight->dpt_airport->icao !!}@if($flight->dpt_time), {!! $flight->dpt_time !!}@endif
                <br />
                <span class="title">ARR&nbsp;</span>
                {!! $flight->arr_airport->icao !!}@if($flight->arr_time), {!! $flight->arr_time !!}@endif
                <br />
                @if($flight->distance)
                    <span class="title">DISTANCE&nbsp;</span>
                    {!! $flight->distance !!} {!! setting('general.distance_unit') !!}
                @endif
                <br />
                @if($flight->level)
                    <span class="title">LEVEL&nbsp;</span>
                    {!! $flight->level !!} {!! setting('general.altitude_unit') !!}
                @endif
            </div>
            <div class="col-sm-7">
                <div class="row">
                    <div class="col-sm-12">
                        <span class="title">ROUTE&nbsp;</span>
                        {!! $flight->route !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endforeach
