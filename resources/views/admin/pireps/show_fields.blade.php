<div class="form-group col-sm-6">
    <div class="box box-solid">
        <div class="box-header with-border">
            {{--<i class="fa fa-text-width"></i>--}}
            <h3 class="box-title">{!! Form::label('dpt_airport_id', 'Dep ICAO') !!}</h3>
        </div>
        <div class="box-body"><p class="lead">
                {!! $pirep->dpt_airport->icao !!} - {!! $pirep->dpt_airport->name !!}
        </p></div>
    </div>
</div>

<div class="form-group col-sm-6">
    <div class="box box-solid">
        <div class="box-header with-border">
            {{--<i class="fa fa-text-width"></i>--}}
            <h3 class="box-title">{!! Form::label('arr_airport_id', 'Arrival ICAO') !!}</h3>
        </div>
        <div class="box-body"><p class="lead">
                {!! $pirep->arr_airport->icao !!} - {!! $pirep->arr_airport->name !!}
            </p>
        </div>
    </div>
</div>


<div class="form-group col-sm-12">
    <div class="box box-primary">
        <div class="box-body">

            <!-- User Id Field -->
            <div class="form-group">
                {!! Form::label('user_id', 'Pilot:') !!}
                <p>{!! $pirep->user->name !!}</p>
            </div>

            <!-- Flight Id Field -->
            <div class="form-group">
                {!! Form::label('flight_id', 'Flight Id:') !!}
                <p>
                    <a href="{!! route('admin.flights.show', [$pirep->flight_id]) !!}" target="_blank">
                        {!! $pirep->airline->code !!}
                        @if($pirep->flight_id)
                            {!! $pirep->flight->flight_number !!}
                        @else
                            {!! $pirep->flight_number !!}
                        @endif
                    </a>
                </p>
            </div>

            <!-- Aircraft Id Field -->
            <div class="form-group">
                {!! Form::label('aircraft_id', 'Aircraft:') !!}
                <p>{!! $pirep->aircraft->subfleet->name !!}, {!! $pirep->aircraft->name !!}
                    ({!! $pirep->aircraft->registration !!})
                </p>
            </div>

            <!-- Flight Time Field -->
            <div class="form-group">
                {!! Form::label('flight_time', 'Flight Time:') !!}
                <p>{!! Utils::secondsToTime($pirep->flight_time) !!}</p>
            </div>

            <!-- Level Field -->
            <div class="form-group">
                {!! Form::label('level', 'Level:') !!}
                <p>{!! $pirep->level !!}</p>
            </div>

            <!-- Route Field -->
            <div class="form-group">
                {!! Form::label('route', 'Route:') !!}
                <p>{!! $pirep->route !!}</p>
            </div>

            <!-- Notes Field -->
            <div class="form-group">
                {!! Form::label('notes', 'Notes:') !!}
                <p>{!! $pirep->notes !!}</p>
            </div>

            <!-- Raw Data Field -->
            <div class="form-group">
                {!! Form::label('raw_data', 'Raw Data:') !!}
                <p>{!! $pirep->raw_data !!}</p>
            </div>

            <!-- Created At Field -->
            <div class="form-group">
                {!! Form::label('created_at', 'Created At:') !!}
                <p>{!! $pirep->created_at !!}</p>
            </div>

            <!-- Updated At Field -->
            <div class="form-group">
                {!! Form::label('updated_at', 'Updated At:') !!}
                <p>{!! $pirep->updated_at !!}</p>
            </div>
        </div>
    </div>
</div>
