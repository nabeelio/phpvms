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
