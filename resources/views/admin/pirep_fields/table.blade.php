<table class="table table-responsive" id="pirepFields-table">
    <thead>
        <th>Name</th>
        <th>Required</th>
        <th colspan="3">Action</th>
    </thead>
    <tbody>
    @foreach($fields as $field)
        <tr>
            <td>{!! $field->name !!}</td>
            <td>{!! $field->required !!}</td>
            <td>
                {!! Form::open(['route' => ['admin.pirepfields.destroy', $field->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    {{--<a href="{!! route('admin.pirepfields.show', [$field->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('admin.pirepfields.edit', [$field->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>--}}
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
