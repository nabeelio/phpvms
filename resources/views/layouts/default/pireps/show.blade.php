@extends('app')
@section('title', 'PIREP '.$pirep->ident)

@section('content')
    <div class="row">
        <div class="col-8">
            <div class="row">
                <div class="col-12">
                    <p>
                        <h2 style="margin-bottom: 5px;">{{$pirep->airline->code}}{{ $pirep->ident }}</h2>
                        <p>
                            @if($pirep->state === PirepState::IN_PROGRESS)

                            @else
                                Arrived {{$pirep->created_at->diffForHumans()}}
                            @endif
                        </p>
                    </p>
                </div>
            </div>
            <div class="row">
                {{--
                    DEPARTURE INFO
                --}}
                <div class="col-6 text-left">
                    <h4>
                        {{$pirep->dpt_airport->location}}
                    </h4>
                    <p>
                        <a href="{{route('frontend.airports.show', ['id' => $pirep->dpt_airport_id])}}">
                            {{ $pirep->dpt_airport->full_name }} ({{  $pirep->dpt_airport_id }})</a>
                        <br/>
                        @if($pirep->block_off_time)
                            {{ $pirep->block_off_time->toDayDateTimeString() }}
                        @endif
                    </p>
                </div>

                {{--
                    ARRIVAL INFO
                --}}
                <div class="col-6 text-right">
                    <h4>
                        {{$pirep->arr_airport->location}}
                    </h4>
                    <p>
                        <a href="{{route('frontend.airports.show', ['id' => $pirep->arr_airport_id])}}">
                            {{ $pirep->arr_airport->full_name }} ({{  $pirep->arr_airport_id }})</a>
                        <br/>
                        @if($pirep->block_on_time)
                            {{ $pirep->block_on_time->toDayDateTimeString() }}
                        @endif
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="progress" style="margin: 20px 0;">
                        <div class="progress-bar progress-bar-success" role="progressbar"
                             aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"
                             style="width: {{$pirep->progress_percent}}%;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    @include('pireps.map')
                </div>
            </div>
        </div>

        {{--

        RIGHT SIDEBAR

        --}}

        <div class="col-4">

            <h2>&nbsp;</h2>

            {{-- Show the link to edit if it can be edited --}}
            @if(!$pirep->read_only)
                <div class="float-right" style="margin-bottom: 10px;">
                    <form method="get"
                          action="{{ route('frontend.pireps.edit', ['id' => $pirep->id]) }}"
                          style="display: inline">
                        @csrf
                        <button class="btn btn-info">Edit</button>
                    </form>
                    &nbsp;
                    <form method="post"
                          action="{{ route('frontend.pireps.submit', ['id' => $pirep->id]) }}"
                          style="display: inline">
                        @csrf
                        <button class="btn btn-success">Submit</button>
                    </form>
                </div>
            @endif
            <table class="table table-striped">
                <tr>
                    <td width="30%">State</td>
                    <td>
                        <div class="badge badge-info">
                            {{ PirepStatus::label($pirep->status) }}
                        </div>
                    </td>
                </tr>

                <tr>
                    <td>Source</td>
                    <td>{{ PirepSource::label($pirep->source) }}</td>
                </tr>

                <tr>
                    <td>Flight Type</td>
                    <td>{{ \App\Models\Enums\FlightType::label($pirep->flight_type) }}</td>
                </tr>

                <tr>
                    <td>Filed Route</td>
                    <td>
                        {{ $pirep->route }}
                    </td>
                </tr>

                <tr>
                    <td>Notes</td>
                    <td>
                        {{ $pirep->notes }}
                    </td>
                </tr>

                <tr>
                    <td>Filed On</td>
                    <td>
                        {{ show_datetime($pirep->created_at) }}
                    </td>
                </tr>

            </table>

            @if(count($pirep->fields) > 0 || count($pirep->fares) > 0)
                <div class="separator"></div>
            @endif

            @if(count($pirep->fields) > 0)
                <h5>fields</h5>
                <table class="table table-hover table-condensed">
                    <thead>
                    <th>Name</th>
                    <th>Value</th>
                    </thead>
                    <tbody>
                    @foreach($pirep->fields as $field)
                        <tr>
                            <td>{{ $field->name }}</td>
                            <td>{{ $field->value }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif

            @if(count($pirep->fares) > 0)
                <div class="separator"></div>
            @endif

            {{--
                Show the fares that have been entered
            --}}
            @if(count($pirep->fares) > 0)
                <div class="row">
                    <div class="col-12">
                        <h5>fares</h5>
                        <table class="table table-hover table-condensed">
                            <thead>
                            <th>Class</th>
                            <th>Count</th>
                            </thead>
                            <tbody>
                            @foreach($pirep->fares as $fare)
                                <tr>
                                    <td>{{ $fare->fare->name }} ({{ $fare->fare->code }})</td>
                                    <td>{{ $fare->count }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if(count($pirep->acars_logs) > 0)
        <div class="separator"></div>
        <div class="row">
            <div class="col-12">
                <h5>flight log</h5>
            </div>
            <div class="col-12">
                <table class="table table-hover table-condensed" id="users-table">
                    <tbody>
                    @foreach($pirep->acars_logs as $log)
                        <tr>
                            <td nowrap="true">{{ show_datetime($log->created_at) }}</td>
                            <td>{{ $log->log }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection

