<div class="row">
    <div class="form-group col-sm-12">
        {{--<div class="avatar">
            <img src="{!! $pirep->pilot->gravatar !!}" />
        </div>--}}
        Filed By: <a href="{!! route('admin.users.edit', [$pirep->pilot->id]) !!}" target="_blank">
            {!! $pirep->pilot->pilot_id !!} {!! $pirep->pilot->name !!}
        </a>
    </div>

    <div class="form-group col-sm-6">
        <div>
        {!! Form::label('airline_id', 'Airline') !!}
        {!! Form::select('airline_id', $airlines, null, ['class' => 'form-control select2']) !!}
        </div>
        <br />
        <div>
        {!! Form::label('aircraft_id', 'Aircraft:') !!}
        {!! Form::select('aircraft_id', $aircraft, null, ['class' => 'form-control select2']) !!}
        </div>
    </div>

    <div class="form-group col-sm-6">
        {!! Form::label('flight_number', 'Flight Number/Route Code/Leg') !!}
        {!! Form::text('flight_number', null, ['placeholder' => 'Flight Number', 'class' => 'form-control']) !!}
        {!! Form::text('route_code', null, ['placeholder' => 'Code (optional)', 'class' => 'form-control']) !!}
        {!! Form::text('route_leg', null, ['placeholder' => 'Leg (optional)', 'class' => 'form-control']) !!}
    </div>

    <div class="form-group col-sm-6">
        {!! Form::label('dpt_airport_id', 'Departure Airport:') !!}
        {!! Form::select('dpt_airport_id', $airports, null, ['class' => 'form-control select2']) !!}
    </div>

    <div class="form-group col-sm-6">
        {!! Form::label('arr_airport_id', 'Arrival Airport:') !!}
        {!! Form::select('arr_airport_id', $airports, null, ['class' => 'form-control select2']) !!}
    </div>

    <!-- Flight Time Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('flight_time', 'Flight Time (hours & minutes):') !!}
        <div class="">
            {!! Form::number('hours', null, ['class' => 'form-control', 'placeholder' => 'hours']) !!}
            {!! Form::number('minutes', null, ['class' => 'form-control', 'placeholder' => 'minutes']) !!}
        </div>
    </div>

    <!-- Level Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('level', 'Flight Level:') !!}
        {!! Form::text('level', null, ['class' => 'form-control']) !!}
    </div>

    <!-- Route Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('route', 'Route:') !!}
        {!! Form::text('route', null, ['class' => 'form-control']) !!}
    </div>

    <!-- Notes Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('notes', 'Notes:') !!}
        {!! Form::textarea('notes', null, ['class' => 'form-control']) !!}
    </div>

    <!-- Raw Data Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('raw_data', 'Raw Data:') !!}
        {!! Form::textarea('raw_data', null, ['class' => 'form-control', 'disabled']) !!}
    </div>

<!-- Submit Field -->
    <div class="form-group col-sm-12">
        <div class="pull-right">
            {!! Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) !!}
            <a href="{!! route('admin.pireps.index') !!}" class="btn btn-warn">Cancel</a>
        </div>
    </div>
</div>
