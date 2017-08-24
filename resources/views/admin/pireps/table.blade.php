@foreach($pireps as $pirep)
<div class="card border-blue-bottom">
    <div class="card-block" style="min-height: 0px">
        <div class="row">
            <div class="col-sm-2 text-center">
                <h5>
                    <a class="text-c"
                       href="{!! route('admin.pireps.show', [$pirep->id]) !!}">
                        {!! $pirep->airline->code !!}
                        @if($pirep->flight_id)
                            {!! $pirep->flight->flight_number !!}
                        @else
                            {!! $pirep->flight_number !!}
                        @endif
                    </a>
                </h5>
                <div>
                    @if($pirep->status == config('enums.pirep_status.PENDING'))
                        <div class="badge badge-warning">Pending</div>
                    @elseif($pirep->status == config('enums.pirep_status.ACCEPTED'))
                        <div class="badge badge-success">Accepted</div>
                    @else
                        <div class="badge badge-danger">Rejected</div>
                    @endif
                </div>
            </div>
            <div class="col-sm-10">
                <div class="row">
                    <div class="col-sm-4">
                        <div>
                            <span class="description">DEP&nbsp;</span>
                            {!! $pirep->dpt_airport->icao !!}&nbsp;
                            <span class="description">ARR&nbsp;</span>
                            {!! $pirep->arr_airport->icao !!}&nbsp;
                        </div>
                        <div><span class="description">Flight Time&nbsp;</span>
                            {!! Utils::secondsToTime($pirep->flight_time) !!}
                        </div>
                        <div><span class="description">Aircraft&nbsp;</span>
                            {!! $pirep->aircraft->registration !!}
                            ({!! $pirep->aircraft->name !!})
                        </div>
                        <div>
                            <span class="description">Flight Level&nbsp;</span>
                            {!! $pirep->level !!}
                        </div>
                        <div>
                            <span class="description">File Date&nbsp;</span>
                            {!! $pirep->created_at !!}
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <span class="description">more data&nbsp;</span>
                    </div>
                    <div class="col-sm-4">
                        <span class="description">more data&nbsp;</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 text-right">
                <button href="#" class="btn btn-info">Accept</button>
                <button href="#" class="btn btn-danger">Reject</button>
            </div>
        </div>
    </div>
</div>
@endforeach

{{--
<table class="table table-hover table-responsive" id="pireps-table">
    <thead>
        <th>Pilot</th>
        <th>Flight</th>
        <th>Aircraft</th>
        <th>Flight Time</th>
        <th>Level</th>
        <th></th>
    </thead>
    <tbody>
    @foreach($pireps as $pirep)
        <tr>
            <td>{!! $pirep->user->name !!}</td>
            <td>
                @if($pirep->flight)
                <a href="{!! route('admin.flights.show', ['id' => $pirep->flight_id]) !!}">
                {!! $pirep->flight->airline->code !!}{!! $pirep->flight->flight_number !!}
                </a>
                @else
                -
                @endif
            </td>
            <td>{!! $pirep->aircraft->registration !!} ({!! $pirep->aircraft->name !!})</td>
            <td>{!! Utils::secondsToTime($pirep->flight_time) !!}</td>
            <td>{!! $pirep->level !!}</td>
            <td style="text-align: right;">
                {!! Form::open(['route' => ['admin.pireps.destroy', $pirep->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('admin.pireps.show', [$pirep->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('admin.pireps.edit', [$pirep->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
        @if($pirep->notes)
        <tr>
            <td>&nbsp;</td>
            <td colspan="8"><strong>Notes:</strong> {!! $pirep->notes !!}</td>
        </tr>
        @endif
    @endforeach
    </tbody>
</table>
--}}
