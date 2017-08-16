<!-- Flight Id Field -->
{{--<div class="form-group col-sm-6">
    {!! Form::label('flight_id', 'Flight ID:') !!}
    {!! Form::text('flight_id', null, ['class' => 'form-control']) !!}
</div>--}}
<div class="row">

    <div class="col-sm-6">

        <p class="description">Airline</p>
        <div class="input-group form-group">
            {!! Form::select('airline_id', $airlines, null, ['class' => 'custom-select select2']) !!}
        </div>

        <p class="description">Aircraft</p>
        <div class="input-group form-group">
            {!! Form::select('aircraft_id', $aircraft, null, ['class' => 'custom-select select2']) !!}
        </div>

        <p class="description">Origin Airport</p>
        <div class="input-group form-group">
            {!! Form::select('dep_airport_id', $airports, null, ['class' => 'custom-select select2']) !!}
        </div>

        <p class="description">Arrival Airport</p>
        <div class="input-group form-group">
            {!! Form::select('arr_airport_id', $airports, null, ['class' => 'custom-select select2']) !!}
        </div>

        <!-- Flight Time Field -->
        <p class="description">Flight Time</p>
        <div class="input-group form-group-no-border">
            {!! Form::text('hours', null, ['class' => 'form-control', 'placeholder' => 'hours']) !!}
        </div>
        <div class="input-group form-group-no-border">
            {!! Form::text('minutes', null, ['class' => 'form-control', 'placeholder' => 'minutes']) !!}
        </div>

        <!-- Level Field -->
        <p class="description">Flight Level</p>
        <div class="input-group form-group">
            <span class="input-group-addon">
                <i class="now-ui-icons users_single-02"></i>
            </span>
            {!! Form::number('level', null, ['class' => 'form-control', 'placeholder' => 'Flight Level']) !!}
        </div>

        <!-- Route Field -->
        <p class="description">Route</p>
        <div class="input-group form-group">

            {!! Form::textarea('route', null, ['class' => 'form-control', 'placeholder' => 'Route']) !!}
        </div>

    </div>
    <div class="col-sm-6">

        <!-- optional fields -->

        @foreach($pirepfields as $field)
            <p class="description text-uppercase">{!! $field->name !!}</p>
            <div class="input-group form-group">
                <!--<span class="input-group-addon">
                    <i class="now-ui-icons users_single-02"></i>
                </span>-->
                {!! Form::text('field_'.$field->id, null, [
                        'class' => 'form-control',
                        'required' => $field->required,
                    ]) !!}
            </div>
        @endforeach

        <p class="description">Notes</p>
        <div class="input-group form-group">
            {!! Form::textarea('notes', null, ['class' => 'form-control', 'placeholder' => 'Notes']) !!}
        </div>
    </div>

    <div class="col-sm-12">
        <div class="float-right">
        <div class="form-group">
            {!! Form::submit('Submit PIREP', ['class' => 'btn btn-primary']) !!}
        </div>
        </div>
    </div>

</div>
