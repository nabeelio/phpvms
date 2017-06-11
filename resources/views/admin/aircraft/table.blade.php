<table class="table table-responsive" id="aircrafts-table">
    <thead>
        <th>ICAO</th>
        <th>Name</th>
        <th>Registration</th>
        <th>Active</th>
        <th colspan="3">Action</th>
    </thead>
    <tbody>
    @foreach($aircraft as $ac)
        <tr>
            <td><a href="{!! route('admin.aircraft.show', [$ac->id]) !!}">{!! $ac->icao !!}</a></td>
            <td>{!! $ac->name !!}</td>
            <td>{!! $ac->registration !!}</td>
            <td>{!! $ac->active !!}</td>
            <td>
                {!! Form::open(['route' => ['admin.aircraft.destroy', $ac->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('admin.aircraft.show', [$ac->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('admin.aircraft.edit', [$ac->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
