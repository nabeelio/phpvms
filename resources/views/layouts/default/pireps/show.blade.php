@extends("layouts.${SKIN_NAME}.app")
@section('title', 'PIREP '.$pirep->ident)

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="description">{!! $pirep->ident !!}</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table">
                <tr>
                    <td>Status</td>
                    <td>
                        @if($pirep->state === PirepState::PENDING)
                            <div class="badge badge-warning">
                        @elseif($pirep->state === PirepState::ACCEPTED)
                            <div class="badge badge-success">
                        @elseif($pirep->state === PirepState::REJECTED)
                            <div class="badge badge-danger">
                        @else
                            <div class="badge badge-info">
                        @endif
                        {!! PirepState::label($pirep->state) !!}</div>

                        <span class="description" style="padding-left: 20px;">
                            source: {!! PirepSource::label($pirep->source) !!}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>Departure/Arrival</td>
                    <td>
                        {!! $pirep->dpt_airport->icao !!} - {!! $pirep->dpt_airport->name !!}
                        <span class="description">to</span>
                        {!! $pirep->arr_airport->icao !!} - {!! $pirep->arr_airport->name !!}
                    </td>
                </tr>

                <tr>
                    <td>Flight Time</td>
                    <td>
                        {!! Utils::minutesToTimeString($pirep->flight_time) !!}
                    </td>
                </tr>

                <tr>
                    <td>Filed Route</td>
                    <td>
                        {!! $pirep->route !!}
                    </td>
                </tr>

                <tr>
                    <td>Notes</td>
                    <td>
                        {!! $pirep->notes !!}
                    </td>
                </tr>

                <tr>
                    <td>Filed On</td>
                    <td>
                        {!! show_datetime($pirep->created_at) !!}
                    </td>
                </tr>

            </table>
        </div>
    </div>

    @if(count($pirep->fields) > 0)
        <div class="row">
            <div class="col-md-12">
                <h3 class="description">fields</h3>
                <table class="table">
                    <thead>
                        <th>Name</th>
                        <th>Value</th>
                    </thead>
                    <tbody>
                    @foreach($pirep->fields as $field)
                        <tr>
                            <td>{!! $field->name !!}</td>
                            <td>{!! $field->value !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @include("layouts.${SKIN_NAME}.pireps.map")

    @if(count($pirep->acars_logs) > 0)
        <br /><br />
        <div class="row clear">
            <div class="col-12">
                <h3 class="description">flight log</h3>
            </div>
            <div class="col-12">
                <table class="table table-hover" id="users-table">
                    <tbody>
                    @foreach($pirep->acars_logs as $log)
                        <tr>
                            <td nowrap="true">{!! show_datetime($log->created_at) !!}</td>
                            <td>{!! $log->log !!}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection

