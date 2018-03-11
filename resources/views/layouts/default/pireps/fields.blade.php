{{--

NOTE ABOUT THIS VIEW

The fields that are marked "read-only", make sure the read-only status doesn't change!
If you make those fields editable, after they're in a read-only state, it can have
an impact on your stats and financials, and will require a recalculation of all the
flight reports that have been filed. You've been warned!

--}}
@if($read_only)
    <div class="row">
        <div class="col-sm-12">
            @component("components.info")
                Once a PIREP has been accepted/rejected, certain fields go into read-only mode.
            @endcomponent
        </div>
    </div>
@endif
<div class="row">
    <div class="col-12">
        <table class="table table-full-width">
            <thead>

            </thead>
            <tbody>
            <tr>
                <td>Airline</td>
                <td>
                    @if($read_only)
                        <p>{!! $pirep->airline->name !!}</p>
                        {!! Form::hidden('airline_id') !!}
                    @else
                        <div class="input-group form-group">
                            {!! Form::select('airline_id', $airline_list, null, [
                                'class' => 'custom-select select2',
                                'readonly' => $read_only]) !!}
                        </div>
                        <p class="text-danger">{{ $errors->first('airline_id') }}</p>
                    @endif
                </td>
            </tr>

            <tr>
                <td>Flight Number/Code/Leg</td>
                <td>
                    @if($read_only)
                        <p>{!! $pirep->ident !!}
                            {!! Form::hidden('flight_number') !!}
                            {!! Form::hidden('flight_code') !!}
                            {!! Form::hidden('flight_leg') !!}
                        </p>
                    @else
                        <div class="input-group form-group" style="max-width: 400px;">
                            {!! Form::text('flight_number', null, [
                                    'placeholder' => 'Flight Number',
                                    'class' => 'form-control',
                                    'readonly' => $read_only]) !!}

                            {!! Form::text('route_code', null, [
                                    'placeholder' => 'Code (optional)',
                                    'class' => 'form-control',
                                    'readonly' => $read_only]) !!}

                            {!! Form::text('route_leg', null, [
                                    'placeholder' => 'Leg (optional)',
                                    'class' => 'form-control',
                                    'readonly' => $read_only]) !!}
                        </div>
                        <p class="text-danger">{{ $errors->first('flight_number') }}</p>
                        <p class="text-danger">{{ $errors->first('route_code') }}</p>
                        <p class="text-danger">{{ $errors->first('route_leg') }}</p>
                    @endif
                </td>
            </tr>

            <tr>
                <td>Aircraft</td>
                <td>
                    @if($read_only)
                        <p>{!! $pirep->aircraft->name !!}</p>
                        {!! Form::hidden('aircraft_id') !!}
                    @else
                        <div class="input-group form-group">
                            {{-- You probably don't want to change this ID if you want the fare select to work --}}
                            {!! Form::select('aircraft_id', $aircraft_list, null, [
                                'id' => 'aircraft_select',
                                'class' => 'custom-select select2',
                                'readonly' => $read_only
                                ]) !!}
                        </div>
                        <p class="text-danger">{{ $errors->first('aircraft_id') }}</p>
                    @endif
                </td>
            </tr>

            <tr>
                <td>Origin Airport</td>
                <td>
                    @if($read_only)
                        <p>{!! $pirep->dpt_airport->id !!} - {!! $pirep->dpt_airport->name !!}</p>
                        {!! Form::hidden('dpt_airport_id') !!}
                    @else
                        <div class="input-group form-group">
                            {!! Form::select('dpt_airport_id', $airport_list, null, [
                                    'class' => 'custom-select select2',
                                    'readonly' => $read_only
                                    ]) !!}
                        </div>
                        <p class="text-danger">{{ $errors->first('dpt_airport_id') }}</p>
                    @endif
                </td>
            </tr>

            <tr>
                <td>Arrival Airport</td>
                <td>
                    @if($read_only)
                        <p>{!! $pirep->arr_airport->id !!}
                            - {!! $pirep->arr_airport->name !!}</p>
                        {!! Form::hidden('arr_airport_id') !!}
                    @else
                        <div class="input-group form-group">
                            {!! Form::select('arr_airport_id', $airport_list, null, [
                                    'class' => 'custom-select select2',
                                    'readonly' => $read_only
                                    ]) !!}
                        </div>
                        <p class="text-danger">{{ $errors->first('arr_airport_id') }}</p>
                    @endif
                </td>
            </tr>

            <tr>
                <td class="align-text-top">Flight Time</td>
                <td>
                    @if($read_only)
                        <p>
                            {!! $pirep->hours !!} hours, {!! $pirep->minutes !!} minutes
                            {!! Form::hidden('hours') !!}
                            {!! Form::hidden('minutes') !!}
                        </p>
                    @else
                        <div class="input-group" style="max-width: 200px;">
                            {!! Form::number('hours', null, [
                                    'class' => 'form-control',
                                    'placeholder' => 'hours',
                                    'min' => '0',
                                    'readonly' => $read_only
                                    ]) !!}

                            {!! Form::number('minutes', null, [
                                'class' => 'form-control',
                                'placeholder' => 'minutes',
                                'min' => 0,
                                'readonly' => $read_only
                                ]) !!}
                        </div>
                        <p class="text-danger">{{ $errors->first('hours') }}</p>
                        <p class="text-danger">{{ $errors->first('minutes') }}</p>
                    @endif
                </td>
            </tr>

            {{--
            Write out the custom fields, and label if they're required
            --}}
            @foreach($pirep_fields as $field)
                <tr>
                    <td>
                        {!! $field->name !!}
                        @if($field->required === true)
                            <span class="text-danger">*</span>
                        @endif
                    </td>
                    <td>
                        <div class="input-group form-group">
                            {!! Form::text($field->slug, null, [
                                'class' => 'form-control'
                                ]) !!}
                        </div>
                        <p class="text-danger">{{ $errors->first($field->slug) }}</p>
                    </td>
                </tr>
            @endforeach

            <tr>
                <td class="align-text-top">Route</td>
                <td>
                    <div class="input-group form-group">
                        {!! Form::textarea('route', null, ['class' => 'form-control', 'placeholder' => 'Route']) !!}
                    </div>
                    <p class="text-danger">{{ $errors->first('route') }}</p>
                </td>
            </tr>

            <tr>
                <td class="align-text-top"><p class="">Notes</p></td>
                <td>
                    <div class="input-group form-group">
                        {!! Form::textarea('notes', null, ['class' => 'form-control', 'placeholder' => 'Notes']) !!}
                    </div>
                    <p class="text-danger">{{ $errors->first('notes') }}</p>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    {{-- You don't want to change this ID unless you don't want the fares form to work :) --}}
    <div id="fares_container" class="col-sm-12">
        @include("pireps.fares")
    </div>
</div>
<div class="row">

    <div class="col-sm-12">
        <div class="float-right">
            <div class="form-group">
                {!! Form::submit('Save PIREP', ['class' => 'btn btn-primary']) !!}
            </div>
        </div>
    </div>

</div>
