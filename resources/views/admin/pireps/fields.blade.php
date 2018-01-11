<div class="row">
    <div class="form-group col-sm-12">
        {{--<div class="avatar">
            <img src="{!! $pirep->pilot->gravatar !!}" />
        </div>--}}
        Filed By: <a href="{!! route('admin.users.edit', [$pirep->pilot->id]) !!}" target="_blank">
            {!! $pirep->pilot->pilot_id !!} {!! $pirep->pilot->name !!}
        </a>
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-6">
        {!! Form::label('flight_number', 'Flight Number/Route Code/Leg') !!}
        <div class="row">
            <div class="col-sm-4">
                {!! Form::text('flight_number', null, ['placeholder' => 'Flight Number', 'class' => 'form-control']) !!}
            </div>
            <div class="col-sm-4">
                {!! Form::text('route_code', null, ['placeholder' => 'Code (optional)', 'class' => 'form-control']) !!}
            </div>
            <div class="col-sm-4">
                {!! Form::text('route_leg', null, ['placeholder' => 'Leg (optional)', 'class' => 'form-control']) !!}
            </div>
        </div>
    </div>
    <div class="form-group col-sm-6">
        {!! Form::label('airline_id', 'Airline') !!}
        <div class="row">
            <div class="col-sm-12">
                {!! Form::select('airline_id', $airlines, null, ['class' => 'form-control select2']) !!}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-4">
        {!! Form::label('aircraft_id', 'Aircraft:') !!}
        {!! Form::select('aircraft_id', $aircraft, null, ['class' => 'form-control select2']) !!}
    </div>
    <div class="form-group col-sm-4">
        {!! Form::label('dpt_airport_id', 'Departure Airport:') !!}
        {!! Form::select('dpt_airport_id', $airports, null, ['class' => 'form-control select2']) !!}
    </div>

    <div class="form-group col-sm-4">
        {!! Form::label('arr_airport_id', 'Arrival Airport:') !!}
        {!! Form::select('arr_airport_id', $airports, null, ['class' => 'form-control select2']) !!}
    </div>
</div>
<div class="row">
    <!-- Flight Time Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('flight_time', 'Flight Time (hours & minutes):') !!}
        <div class="row">
            <div class="col-sm-6">
                {!! Form::number('hours', null, ['class' => 'form-control', 'placeholder' => 'hours']) !!}
            </div>
            <div class="col-sm-6">
                {!! Form::number('minutes', null, ['class' => 'form-control', 'placeholder' => 'minutes']) !!}
            </div>
        </div>
    </div>

    <!-- Level Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('level', 'Flight Level:') !!}
        <div class="row">
            <div class="col-sm-12">
                {!! Form::text('level', null, ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <!-- Route Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('route', 'Route:') !!}
        {!! Form::textarea('route', null, ['class' => 'form-control']) !!}
    </div>

    <!-- Notes Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('notes', 'Notes:') !!}
        {!! Form::textarea('notes', null, ['class' => 'form-control']) !!}
    </div>
</div>
<div class="row">
{{--    <!-- Raw Data Field -->
    <div class="form-group col-sm-6">
        {!! Form::label('raw_data', 'Raw Data:') !!}
        {!! Form::textarea('raw_data', null, ['class' => 'form-control', 'disabled']) !!}
    </div>--}}

<!-- Submit Field -->
    <div class="form-group col-sm-12">
        <div class="pull-right">
            {!! Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) !!}
            <a href="{!! route('admin.pireps.index') !!}" class="btn btn-warn">Cancel</a>
        </div>
    </div>
</div>
