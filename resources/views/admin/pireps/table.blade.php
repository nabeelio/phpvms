<table class="table table-responsive" id="pireps-table">
    <thead>
        <th>User Id</th>
        <th>Flight Id</th>
        <th>Aircraft Id</th>
        <th>Flight Time</th>
        <th>Level</th>
        <th>Route</th>
        <th>Notes</th>
        <th>Raw Data</th>
        <th colspan="3">Action</th>
    </thead>
    <tbody>
    @foreach($pireps as $pirep)
        <tr>
            <td>{!! $pirep->user_id !!}</td>
            <td>{!! $pirep->flight_id !!}</td>
            <td>{!! $pirep->aircraft_id !!}</td>
            <td>{!! $pirep->flight_time !!}</td>
            <td>{!! $pirep->level !!}</td>
            <td>{!! $pirep->route !!}</td>
            <td>{!! $pirep->notes !!}</td>
            <td>{!! $pirep->raw_data !!}</td>
            <td>
                {!! Form::open(['route' => ['admin.pireps.destroy', $pirep->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('admin.pireps.show', [$pirep->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('admin.pireps.edit', [$pirep->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
