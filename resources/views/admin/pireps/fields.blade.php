@if($read_only)
   <div class="row">
       <div class="col-sm-12">
           @component('admin.components.info')
               Once a PIREP has been accepted/rejected, certain fields go into read-only mode.
           @endcomponent
       </div>
   </div>
@endif
<div class="row">
    <div class="form-group col-sm-6">
        {{ Form::label('flight_number', 'Flight Number/Route Code/Leg') }}
        @if($read_only)
            <p>{{ $pirep->ident }}
                {{ Form::hidden('flight_number') }}
                {{ Form::hidden('flight_code') }}
                {{ Form::hidden('flight_leg') }}
            </p>
        @else
            <div class="row">
                <div class="col-sm-4">
                    {{ Form::text('flight_number', null, [
                            'placeholder' => 'Flight Number',
                            'class' => 'form-control']) }}
                    <p class="text-danger">{{ $errors->first('flight_number') }}</p>
                </div>
                <div class="col-sm-4">
                    {{ Form::text('route_code', null, [
                            'placeholder' => 'Code (optional)',
                            'class' => 'form-control']) }}
                    <p class="text-danger">{{ $errors->first('route_code') }}</p>
                </div>
                <div class="col-sm-4">
                    {{ Form::text('route_leg', null, [
                            'placeholder' => 'Leg (optional)',
                            'class' => 'form-control']) }}
                    <p class="text-danger">{{ $errors->first('route_leg') }}</p>
                </div>
            </div>
        @endif
    </div>
    <div class="form-group col-sm-6">
        <p class="description">Filed Via:</p>
        {{ PirepSource::label($pirep->source) }}
        @if(filled($pirep->source_name))
            ({{ $pirep->source_name }})
        @endif
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-3">
        {{ Form::label('airline_id', 'Airline') }}
        @if($read_only)
            <p>{{ $pirep->airline->name }}</p>
            {{ Form::hidden('airline_id') }}
        @else
            {{ Form::select('airline_id', $airlines_list, null, [
                    'class' => 'form-control select2',
                    'readonly' => $read_only]) }}
            <p class="text-danger">{{ $errors->first('airline_id') }}</p>
        @endif
    </div>
    <div class="form-group col-sm-3">
        {{ Form::label('aircraft_id', 'Aircraft:') }}
        @if($read_only)
            <p>{{ $pirep->aircraft->name }}</p>
            {{ Form::hidden('aircraft_id') }}
        @else
            {{ Form::select('aircraft_id', $aircraft_list, null, [
                    'id' => 'aircraft_select',
                    'class' => 'form-control select2',
                    'readonly' => $read_only
                ]) }}
            <p class="text-danger">{{ $errors->first('aircraft_id') }}</p>
        @endif
    </div>
    <div class="form-group col-sm-3">
        {{ Form::label('dpt_airport_id', 'Departure Airport:') }}
        @if($read_only)
            <p>{{ $pirep->dpt_airport->id }} - {{ $pirep->dpt_airport->name }}</p>
            {{ Form::hidden('dpt_airport_id') }}
        @else
            {{ Form::select('dpt_airport_id', $airports_list, null, [
                    'class' => 'form-control select2',
                    'readonly' => $read_only]) }}
            <p class="text-danger">{{ $errors->first('dpt_airport_id') }}</p>
        @endif
    </div>

    <div class="form-group col-sm-3">
        {{ Form::label('arr_airport_id', 'Arrival Airport:') }}
        @if($read_only)
            <p>{{ $pirep->arr_airport->id }} - {{ $pirep->arr_airport->name }}</p>
            {{ Form::hidden('arr_airport_id') }}
        @else
            {{ Form::select('arr_airport_id', $airports_list, null, ['class' => 'form-control select2']) }}
            <p class="text-danger">{{ $errors->first('arr_airport_id') }}</p>
        @endif
    </div>
</div>
<div class="row">
    <!-- Flight Time Field -->
    <div class="form-group col-sm-6">
        {{ Form::label('flight_time', 'Flight Time (hours & minutes):') }}
        @if($read_only)
            <p>
                {{ $pirep->hours }} hours, {{ $pirep->minutes }} minutes
                {{ Form::hidden('hours') }}
                {{ Form::hidden('minutes') }}
            </p>
        @else
            <div class="row">
                <div class="col-sm-6">
                    {{ Form::number('hours', null, [
                            'class' => 'form-control',
                            'placeholder' => 'hours',
                            'readonly' => $read_only]) }}
                </div>
                <div class="col-sm-6">
                    {{ Form::number('minutes', null, [
                            'class' => 'form-control',
                            'placeholder' => 'minutes',
                            'readonly' => $read_only]) }}
                </div>
                <p class="text-danger">{{ $errors->first('hours') }}</p>
                <p class="text-danger">{{ $errors->first('minutes') }}</p>
            </div>
        @endif
    </div>

    <!-- Level Field -->
    <div class="form-group col-sm-6">
        {{ Form::label('level', 'Flight Level:') }}
        <div class="row">
            <div class="col-sm-12">
                {{ Form::number('level', null, ['class' => 'form-control', 'min' => 0]) }}
                <p class="text-danger">{{ $errors->first('level') }}</p>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <!-- Route Field -->
    <div class="form-group col-sm-6">
        {{ Form::label('route', 'Route:') }}
        {{ Form::textarea('route', null, ['class' => 'form-control']) }}
        <p class="text-danger">{{ $errors->first('route') }}</p>
    </div>

    <!-- Notes Field -->
    <div class="form-group col-sm-6">
        {{ Form::label('notes', 'Notes:') }}
        {{ Form::textarea('notes', null, ['class' => 'form-control']) }}
        <p class="text-danger">{{ $errors->first('notes') }}</p>
    </div>
</div>

{{--
    FARES
--}}
<div class="row">
    <div class="col-sm-12">
        <hr>
        <h3>fares</h3>
        {{-- You don't want to change this ID unless you don't want the fares form to work :) --}}
        <div id="fares_container">
            @include('admin.pireps.fares')
        </div>
    </div>
</div>

{{--
    CUSTOM FIELDS
--}}

<div class="row">
    <div class="col-sm-12">
        <hr>
        <h3>field values</h3>
        {{-- You don't want to change this ID unless you don't want the fares form to work :) --}}
        @include('admin.pireps.field_values')
    </div>
</div>

<div class="row">
    <div class="form-group col-sm-12">
        <div class="pull-right">
            {{ Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-success']) }}
            <a href="{{ route('admin.pireps.index') }}" class="btn btn-warn">Cancel</a>
        </div>
    </div>
</div>
