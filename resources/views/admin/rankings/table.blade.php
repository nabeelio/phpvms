<table class="table table-responsive" id="rankings-table">
    <thead>
        <th>Name</th>
        <th>Hours</th>
        <th>Auto Approve Acars</th>
        <th>Auto Approve Manual</th>
        <th>Auto Promote</th>
        <th colspan="3">Action</th>
    </thead>
    <tbody>
    @foreach($rankings as $ranking)
        <tr>
            <td>{!! $ranking->name !!}</td>
            <td>{!! $ranking->hours !!}</td>
            <td>{!! $ranking->auto_approve_acars !!}</td>
            <td>{!! $ranking->auto_approve_manual !!}</td>
            <td>{!! $ranking->auto_promote !!}</td>
            <td>
                {!! Form::open(['route' => ['admin.rankings.destroy', $ranking->$PRIMARY_KEY_NAME$], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('admin.rankings.show', [$ranking->$PRIMARY_KEY_NAME$]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('admin.rankings.edit', [$ranking->$PRIMARY_KEY_NAME$]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>