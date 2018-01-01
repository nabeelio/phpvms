
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
                    </td>
                </tr>

                <tr>
                    <td>Aircraft</td>
                    <td>
                        <div class="input-group form-group">
                            {!! Form::select('aircraft_id', $aircraft, null, ['class' => 'custom-select select2']) !!}
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Origin Airport</td>
                    <td>
                        <div class="input-group form-group">
                            {!! Form::select('dpt_airport_id', $airports, null, ['class' => 'custom-select select2']) !!}
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Arrival Airport</td>
                    <td>
                        <div class="input-group form-group">
                            {!! Form::select('arr_airport_id', $airports, null, ['class' => 'custom-select select2']) !!}
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="align-text-top">Flight Time</td>
                    <td>
                        <div class="input-group" style="max-width: 200px;">
                            {!! Form::number('hours', null, ['class' => 'form-control', 'placeholder' => 'hours']) !!}
                            {!! Form::number('minutes', null, ['class' => 'form-control', 'placeholder' => 'minutes']) !!}
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="align-text-top">Route</td>
                    <td>
                        <div class="input-group form-group">
                            {!! Form::textarea('route', null, ['class' => 'form-control', 'placeholder' => 'Route']) !!}
                        </div>
                    </td>
                </tr>

                {{--
                Write out the custom fields, and label if they're required
                --}}
                @foreach($pirepfields as $field)
                <tr>
                    <td>
                        {!! $field->name !!}
                        <span class="label label-danger">required</span>
                    </td>
                    <td>
                        <div class="input-group form-group">
                            <!--<span class="input-group-addon">
                                <i class="now-ui-icons users_single-02"></i>
                            </span>-->
                            {!! Form::text('field_'.$field->id, null, [
                                'class' => 'form-control'
                                ]) !!}
                        </div>
                    </td>
                </tr>
                @endforeach

                <tr>
                    <td class="align-text-top"><p class="">Notes</p></td>
                    <td>
                        <div class="input-group form-group">
                            {!! Form::textarea('notes', null, ['class' => 'form-control', 'placeholder' => 'Notes']) !!}
                        </div>
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
