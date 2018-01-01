<div class="row">
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
</div>
<div class="row">
    <div class="col-md-12">
        <table class="table">
            <tr>
                <td>Pilot</td>
                <td>
                    <p>
                        <a href="{!! route('admin.users.show', ['id' => $pirep->pilot->id]) !!}"
                            target="_blank">{!! $pirep->user->name !!}</a>
                    </p>
                </td>
            </tr>
            <tr>
                <td>Flight</td>
                <td>
                    <p>
                        <a href="{!! route('admin.flights.show', [$pirep->flight_id]) !!}"
                           target="_blank">
                            {!! $pirep->ident !!}
                        </a>
                    </p>
                </td>
            </tr>
            <tr>
                <td>Aircraft</td>
                <td>
                    <p>{!! $pirep->aircraft->subfleet->name !!}, {!! $pirep->aircraft->name !!}
                        ({!! $pirep->aircraft->registration !!})
                    </p>
                </td>
            </tr>
            <tr>
                <td>Flight Time</td>
                <td><p>{!! Utils::minutesToTimeString($pirep->flight_time) !!}</p></td>
            </tr>
            <tr>
                <td>Flight Level</td>
                <td><p>{!! $pirep->level !!}</p></td>
            </tr>
            <tr>
                <td>Route</td>
                <td>{!! $pirep->route !!}</td>
            </tr>
            <tr>
                <td>Notes</td>
                <td>{!! $pirep->notes !!}</td>
            </tr>
            <tr>
                <td>Filed On</td>
                <td><p>{!! show_datetime($pirep->created_at) !!}</p></td>
            </tr>

            <tr>
                <td>Updated On</td>
                <td><p>{!! show_datetime($pirep->updated_at) !!}</p></td>
            </tr>
        </table>
    </div>
</div>
