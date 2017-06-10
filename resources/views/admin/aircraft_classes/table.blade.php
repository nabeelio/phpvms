<table class="table table-responsive" id="aircraftClasses-table">
    <thead>
        <th>Class</th>
        <th>Name</th>
        <th>Notes</th>
        <th colspan="3">Action</th>
    </thead>
    <tbody>
    @foreach($aircraftClasses as $aircraftClass)
        <tr>
            <td>{!! $aircraftClass->class !!}</td>
            <td>{!! $aircraftClass->name !!}</td>
            <td>{!! $aircraftClass->notes !!}</td>
            <td>
                {!! Form::open(['route' => ['admin.aircraftClasses.destroy', $aircraftClass->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('admin.aircraftClasses.show', [$aircraftClass->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('admin.aircraftClasses.edit', [$aircraftClass->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>