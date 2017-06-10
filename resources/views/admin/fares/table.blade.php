<table class="table table-responsive" id="fares-table">
    <thead>
        <th>Code</th>
        <th>Name</th>
        <th>Price</th>
        <th>Cost</th>
        <th>Notes</th>
        <th>Active</th>
        <th colspan="3">Action</th>
    </thead>
    <tbody>
    @foreach($fares as $fare)
        <tr>
            <td>{!! $fare->code !!}</td>
            <td>{!! $fare->name !!}</td>
            <td>{!! $fare->price !!}</td>
            <td>{!! $fare->cost !!}</td>
            <td>{!! $fare->notes !!}</td>
            <td>{!! $fare->active !!}</td>
            <td>
                {!! Form::open(['route' => ['admin.fares.destroy', $fare->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('admin.fares.show', [$fare->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('admin.fares.edit', [$fare->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>