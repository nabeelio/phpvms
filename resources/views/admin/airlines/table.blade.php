<table class="table table-responsive" id="airlines-table">
    <thead>
        <th>Code</th>
        <th>Name</th>
        <th>Enabled</th>
        <th colspan="3">Action</th>
    </thead>
    <tbody>
    @foreach($airlines as $airlines)
        <tr>
            <td>{!! $airlines->code !!}</td>
            <td>{!! $airlines->name !!}</td>
            <td>{!! $airlines->enabled !!}</td>
            <td>
                {!! Form::open(['route' => ['airlines.destroy', $airlines->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('airlines.show', [$airlines->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('airlines.edit', [$airlines->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>