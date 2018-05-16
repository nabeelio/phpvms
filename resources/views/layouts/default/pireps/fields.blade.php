{{--

NOTE ABOUT THIS VIEW

The fields that are marked "read-only", make sure the read-only status doesn't change!
If you make those fields editable, after they're in a read-only state, it can have
an impact on your stats and financials, and will require a recalculation of all the
flight reports that have been filed. You've been warned!

--}}
@if(!empty($pirep) && $pirep->read_only)
    <div class="row">
        <div class="col-sm-12">
            @component('components.info')
			{{ __('Once a PIREP has been accepted/rejected, certain fields go into read-only mode.') }}
            @endcomponent
        </div>
    </div>
@endif
<div class="row">
    <div class="col-8">
        <div class="form-container">
            <h6><i class="fas fa-info-circle"></i>
                &nbsp;{{ __('Flight Informations') }}
            </h6>
            <div class="form-container-body">
                <div class="row">
                    <div class="col-sm-4">
                        {{ Form::label('airline_id', __('Airline')) }}
                        @if(!empty($pirep) && $pirep->read_only)
                            <p>{{ $pirep->airline->name }}</p>
                            {{ Form::hidden('airline_id') }}
                        @else
                            <div class="input-group input-group form-group">
                                {{ Form::select('airline_id', $airline_list, null, [
                                    'class' => 'custom-select select2',
                                    'style' => 'width: 100%',
                                    'readonly' => (!empty($pirep) && $pirep->read_only),
                                ]) }}
                            </div>
                            <p class="text-danger">{{ $errors->first('airline_id') }}</p>
                        @endif
                    </div>
                    <div class="col-sm-4">
                        {{ Form::label('flight_number', __('Flight Number/Code/Leg')) }}
                        @if(!empty($pirep) && $pirep->read_only)
                            <p>{{ $pirep->ident }}
                                {{ Form::hidden('flight_number') }}
                                {{ Form::hidden('flight_code') }}
                                {{ Form::hidden('flight_leg') }}
                            </p>
                        @else
                            <div class="input-group input-group-sm mb3">
                                {{ Form::text('flight_number', null, [
                                    'placeholder' => __('Flight Number'),
                                    'class' => 'form-control',
                                    'readonly' => (!empty($pirep) && $pirep->read_only),
                                ]) }}
                                &nbsp;
                                {{ Form::text('route_code', null, [
                                    'placeholder' => __('Code (optional)'),
                                    'class' => 'form-control',
                                    'readonly' => (!empty($pirep) && $pirep->read_only),
                                ]) }}
                                &nbsp;
                                {{ Form::text('route_leg', null, [
                                    'placeholder' => __('Leg (optional)'),
                                    'class' => 'form-control',
                                    'readonly' => (!empty($pirep) && $pirep->read_only),
                                ]) }}
                            </div>
                            <p class="text-danger">{{ $errors->first('flight_number') }}</p>
                            <p class="text-danger">{{ $errors->first('route_code') }}</p>
                            <p class="text-danger">{{ $errors->first('route_leg') }}</p>
                        @endif
                    </div>
                    <div class="col-lg-4">
                        {{ Form::label('flight_type', __('Flight Type')) }}
                        @if(!empty($pirep) && $pirep->read_only)
                            <p>{{ \App\Models\Enums\FlightType::label($pirep->flight_type) }}</p>
                            {{ Form::hidden('flight_type') }}
                        @else
                            <div class="form-group">
                                {{ Form::select('flight_type',
                                    \App\Models\Enums\FlightType::select(), null, [
                                        'class' => 'custom-select select2',
                                        'style' => 'width: 100%',
                                        'readonly' => (!empty($pirep) && $pirep->read_only),
                                    ])
                                }}
                            </div>
                            <p class="text-danger">{{ $errors->first('flight_type') }}</p>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-3">
                        {{ Form::label('hours', __('Flight Time')) }}
                        @if(!empty($pirep) && $pirep->read_only)
                            <p>
                                {{ $pirep->hours.' '.__trans_choice('Hour', $pirep->hours) }}, {{ $pirep->minutes.' '.__trans_choice('Minute', $pirep->minutes) }}
                                {{ Form::hidden('hours') }}
                                {{ Form::hidden('minutes') }}
                            </p>
                        @else
                            <div class="input-group input-group-sm" style="max-width: 200px;">
                                {{ Form::number('hours', null, [
                                        'class' => 'form-control',
                                        'placeholder' => __trans_choice('Hour', 2),
                                        'min' => '0',
                                        'readonly' => (!empty($pirep) && $pirep->read_only),
                                ]) }}

                                {{ Form::number('minutes', null, [
                                    'class' => 'form-control',
                                    'placeholder' => __trans_choice('Minute', 2),
                                    'min' => 0,
                                    'readonly' => (!empty($pirep) && $pirep->read_only),
                                    ]) }}
                            </div>
                            <p class="text-danger">{{ $errors->first('hours') }}</p>
                            <p class="text-danger">{{ $errors->first('minutes') }}</p>
                        @endif
                    </div>


                    <div class="col-3">
                        {{--{{ Form::label('submitted_date', __('Date Flown')) }}
                        {{ Form::text('submmitted_date', null, [
                                'placeholder' => __('Departure Date'),
                                'class' => 'form-control',
                                'readonly' => $pirep->read_only]) }}--}}
                    </div>


                    <div class="col-3">
                        {{--{{ Form::label('departure_time', __('Departure Time')) }}
                        {{ Form::text('departure_time', null, [
                                        'placeholder' => __('Departure TIme'),
                                        'class' => 'form-control',
                                        'readonly' => $pirep->read_only]) }}--}}
                    </div>


                    <div class="col-3">
                        {{--{{ Form::label('arrival_time', __('Arrival Time')) }}
                        {{ Form::text('arrival_time', null, [
                                        'placeholder' => __('Arrival TIme'),
                                        'class' => 'form-control',
                                        'readonly' => $pirep->read_only]) }}--}}
                    </div>

                </div>
            </div>
        </div>


        <div class="form-container">
            <h6><i class="fas fa-globe"></i>
                &nbsp;{{ __('Departure/Arrival Informations') }}
            </h6>
            <div class="form-container-body">
                <div class="row">
                    <div class="col-6">
                        {{ Form::label('dpt_airport_id', __('Departure Airport')) }}
                        @if(!empty($pirep) && $pirep->read_only)
                            {{ $pirep->dpt_airport->name }}
                            (<a href="{{route('frontend.airports.show', [
                                    'id' => $pirep->dpt_airport->icao
                                    ])}}">{{$pirep->dpt_airport->icao}}</a>)
                            {{ Form::hidden('dpt_airport_id') }}
                        @else
                            <div class="form-group">
                                {{ Form::select('dpt_airport_id', $airport_list, null, [
                                        'class' => 'custom-select select2',
                                        'style' => 'width: 100%',
                                        'readonly' => (!empty($pirep) && $pirep->read_only),
                                ]) }}
                            </div>
                            <p class="text-danger">{{ $errors->first('dpt_airport_id') }}</p>
                        @endif
                    </div>

                    <div class="col-6">
                        {{ Form::label('arr_airport_id', __('Arrival Airport')) }}
                        @if(!empty($pirep) && $pirep->read_only)
                            {{ $pirep->arr_airport->name }}
                            (<a href="{{route('frontend.airports.show', [
                                    'id' => $pirep->arr_airport->icao
                                    ])}}">{{$pirep->arr_airport->icao}}</a>)
                            {{ Form::hidden('arr_airport_id') }}
                        @else
                            <div class="input-group input-group-sm form-group">
                                {{ Form::select('arr_airport_id', $airport_list, null, [
                                        'class' => 'custom-select select2',
                                        'style' => 'width: 100%',
                                        'readonly' => (!empty($pirep) && $pirep->read_only),
                                ]) }}
                            </div>
                            <p class="text-danger">{{ $errors->first('arr_airport_id') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="form-container">
            <h6><i class="fab fa-avianex"></i>
                &nbsp;{{ __('Aircraft Informations') }}
            </h6>
            <div class="form-container-body">
                <div class="row">
                    <div class="col">
                        {{ Form::label('aircraft_id', __('Aircraft')) }}
                        @if(!empty($pirep) && $pirep->read_only)
                            <p>{{ $pirep->aircraft->name }}</p>
                            {{ Form::hidden('aircraft_id') }}
                        @else
                            <div class="input-group input-group-sm form-group">
                                {{-- You probably don't want to change this ID if you want the fare select to work --}}
                                {{ Form::select('aircraft_id', $aircraft_list, null, [
                                    'id' => 'aircraft_select',
                                    'class' => 'custom-select select2',
                                    'readonly' => (!empty($pirep) && $pirep->read_only),
                                    ]) }}
                            </div>
                            <p class="text-danger">{{ $errors->first('aircraft_id') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="form-container">
            <h6><i class="far fa-comments"></i>
                &nbsp;{{ __('Route') }}
            </h6>
            <div class="form-container-body">
                <div class="row">
                    <div class="col">
                        <div class="input-group input-group-sm form-group">
                            {{ Form::textarea('route', null, ['class' => 'form-control', 'placeholder' => __('Route')]) }}
                            <p class="text-danger">{{ $errors->first('route') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-container">
            <h6><i class="far fa-comments"></i>
                &nbsp;{{ __('Remarks') }}
            </h6>
            <div class="form-container-body">
                <div class="row">
                    <div class="col">
                        <div class="input-group input-group-sm form-group">
                            {{ Form::textarea('notes', null, ['class' => 'form-control', 'placeholder' => __('Notes')]) }}
                            <p class="text-danger">{{ $errors->first('notes') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-4">

        <div class="form-container">
            <h6><i class="fab fa-wpforms"></i>
                &nbsp;{{ __trans_choice('Field', 2) }}
            </h6>
            <div class="form-container-body">

                {{--
                Write out the custom fields, and label if they're required
                --}}
                @foreach($pirep_fields as $field)
                    <tr>
                        <td>
                            {{ $field->name }}
                            @if($field->required === true)
                                <span class="text-danger">*</span>
                            @endif
                        </td>
                        <td>
                            <div class="input-group input-group-sm form-group">
                                {{ Form::text($field->slug, null, [
                                    'class' => 'form-control'
                                    ]) }}
                            </div>
                            <p class="text-danger">{{ $errors->first($field->slug) }}</p>
                        </td>
                    </tr>
                @endforeach
            </div>
        </div>

        <div id="fares_container">
            @include('pireps.fares')
        </div>

    </div>
</div>
<div class="row">

    <div class="col-sm-12">
        <div class="float-right">
            <div class="form-group">

                @if(isset($pirep) && !$pirep->read_only)
                    {{ Form::button(__('Delete PIREP'), [
                        'name' => 'submit',
                        'value' => 'Delete',
                        'class' => 'btn btn-warning',
                        'type' => 'submit'])
                        }}
                @endif

                @if(!isset($pirep) || (filled($pirep) && !$pirep->read_only))
                    {{ Form::button(__('Save PIREP'), [
                        'name' => 'submit',
                        'value' => 'Save',
                        'class' => 'btn btn-info',
                        'type' => 'submit'])
                    }}

                    {{ Form::button(__('Submit PIREP'), [
                        'name' => 'submit',
                        'value' => 'Submit',
                        'class' => 'btn btn-success',
                        'type' => 'submit'])
                    }}
                @endif
            </div>
        </div>
    </div>

</div>
