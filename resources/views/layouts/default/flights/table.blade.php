@foreach($flights as $flight)
<div class="card border-blue-bottom">
    <div class="card-block" style="min-height: 0px">
        <div class="row">
            <div class="col-sm-3">
                <h5>
                    <a class="text-c" href="{!! route('frontend.flights.show', [$flight->id]) !!}">
                        {!! $flight->airline->code !!}{!! $flight->flight_number !!}
                        @if($flight->route_code)
                            (C: {!! $flight->route_code !!} L: {!! $flight->route_leg !!})
                        @endif
                    </a>
                </h5>
            </div>
            <div class="col-sm-9 text-sm-right">
                <!-- use for saved: btn-outline-primary -->
                <button class="btn btn-icon btn-icon-mini btn-round
                               {{ in_array($flight->id, $saved) ? 'btn-danger':'' }}
                               save_flight" x-id="{!! $flight->id !!}" type="button">
                    <i class="now-ui-icons ui-2_favourite-28"></i>
                </button>
            </div>


            <div class="col-sm-3">
                <div class="row">
                    <div class="col-sm-6">
                        <span class="title">DEP&nbsp;</span>
                        {!! $flight->dpt_airport->icao !!}&nbsp;

                        <span class="title">ARR&nbsp;</span>
                        {!! $flight->arr_airport->icao !!}&nbsp;
                    </div>
                    <div  class="col-sm-6 text-right">

                        <span class="description">{!! $flight->dpt_time !!}</span>
                        <span class="description">{!! $flight->arr_time !!}</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-9">
                <div class="row">
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
