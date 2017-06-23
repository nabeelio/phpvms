<table class="table table-responsive" id="subfleets-table">
    <thead>
        <th>Airline</th>
        <th>Name</th>
        <th>Type</th>
        <th colspan="3">Action</th>
    </thead>
    <tbody>
    @foreach($subfleets as $subfleet)
        <tr>
            <td>{!! $subfleet->airline->name !!}</td>
            <td>{!! $subfleet->name !!}</td>
            <td>{!! $subfleet->type !!}</td>
            <td>
                {!! Form::open(['route' => ['admin.subfleets.destroy', $subfleet->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('admin.subfleets.show', [$subfleet->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('admin.subfleets.edit', [$subfleet->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
