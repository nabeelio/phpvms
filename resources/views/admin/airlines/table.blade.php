<table class="table table-responsive" id="airlines-table">
    <thead>
        <th>Code</th>
        <th>Name</th>
        <th>Active?</th>
        <th colspan="3">Action</th>
    </thead>
    <tbody>
    @foreach($airlines as $al)
        <tr>
            <td>{!! $al->code !!}</td>
            <td>{!! $al->name !!}</td>
            <td>{!! $al->active !!}</td>
            <td>
                {!! Form::open(['route' => ['airlines.destroy', $al->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('airlines.show', [$al->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('airlines.edit', [$al->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
