@extends('layouts.default.app')

@section('title', 'PIREP '.$pirep->ident)
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="description">{!! $pirep->getFlightId() !!}</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table">
                <tr>
                    <td>Status</td>
                    <td>
                        @if($pirep->state == PirepState::PENDING)
                            <div class="badge badge-warning ">Pending</div>
                        @elseif($pirep->state === PirepState::ACCEPTED)
                            <div class="badge badge-success">Accepted</div>
                        @else
                            <div class="badge badge-danger">Rejected</div>
                        @endif
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
                    <td>Route</td>
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

            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h3 class="description">fields</h3>
            <table class="table">
                <thead>
                <th>Name</th>
                <th>Value</th>
                <th>Source</th>
                </thead>
                <tbody>
                @foreach($pirep->fields as $field)
                    <tr>
                        <td>{!! $field->name !!}</td>
                        <td>{!! $field->value !!}</td>
                        <td>{!! $field->source !!}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @include('layouts.default.pireps.map')
@endsection

