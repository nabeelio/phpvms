
<div class="row">
    <div class="col-12">
        <table class="table table-full-width">
            <thead>

            </thead>
            <tbody>
                <tr>
                    <td>Airline</td>
                    <td>
                        <div class="input-group form-group">
                            {!! Form::select('airline_id', $airlines, null, ['class' => 'custom-select select2']) !!}
                        </div>
                        <p class="text-danger">{{ $errors->first('airline_id') }}</p>
                    </td>
                </tr>

                <tr>
                    <td>Flight Number/Code/Leg</td>
                    <td>
                        <div class="input-group form-group" style="max-width: 400px;">
                            {!! Form::text('flight_number', null, ['placeholder' => 'Flight Number', 'class' => 'form-control']) !!}
                            {!! Form::text('route_code', null, ['placeholder' => 'Code (optional)', 'class' => 'form-control']) !!}
                            {!! Form::text('route_leg', null, ['placeholder' => 'Leg (optional)', 'class' => 'form-control']) !!}
                        </div>
                        <p class="text-danger">{{ $errors->first('flight_number') }}</p>
                        <p class="text-danger">{{ $errors->first('route_code') }}</p>
                        <p class="text-danger">{{ $errors->first('route_leg') }}</p>
                    </td>
                </tr>

                <tr>
                    <td>Aircraft</td>
                    <td>
                        <div class="input-group form-group">
                            {!! Form::select('aircraft_id', $aircraft, null, ['class' => 'custom-select select2']) !!}
                        </div>
                        <p class="text-danger">{{ $errors->first('aircraft_id') }}</p>
                    </td>
                </tr>

                <tr>
                    <td>Origin Airport</td>
                    <td>
                        <div class="input-group form-group">
                            {!! Form::select('dpt_airport_id', $airports, null, ['class' => 'custom-select select2']) !!}
                        </div>
                        <p class="text-danger">{{ $errors->first('dpt_airport_id') }}</p>
                    </td>
                </tr>

                <tr>
                    <td>Arrival Airport</td>
                    <td>
                        <div class="input-group form-group">
                            {!! Form::select('arr_airport_id', $airports, null, ['class' => 'custom-select select2']) !!}
                        </div>
                        <p class="text-danger">{{ $errors->first('arr_airport_id') }}</p>
                    </td>
                </tr>

                <tr>
                    <td class="align-text-top">Flight Time</td>
                    <td>
                        <div class="input-group" style="max-width: 200px;">
                            {!! Form::number('hours', null, ['class' => 'form-control', 'placeholder' => 'hours']) !!}
                            {!! Form::number('minutes', null, ['class' => 'form-control', 'placeholder' => 'minutes']) !!}
                        </div>
                        <p class="text-danger">{{ $errors->first('hours') }}</p>
                        <p class="text-danger">{{ $errors->first('minutes') }}</p>
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

    <div class="col-sm-12">
        <div class="float-right">
        <div class="form-group">
            {!! Form::submit('Submit PIREP', ['class' => 'btn btn-primary']) !!}
        </div>
        </div>
    </div>

</div>
