<table class="table table-responsive" id="airlines-table">
    <thead>
        <th>Code</th>
        <th>IATA</th>
        <th>Name</th>
        <th class="text-center">Active</th>
        <th class="text-right">Action</th>
    </thead>
    <tbody>
    @foreach($airlines as $al)
        <tr>
            <td>{!! $al->code !!}</td>
            <td>{!! $al->iata !!}</td>
            <td>{!! $al->name !!}</td>
            <td class="text-center">
                @if($al->active == 1)
                    <span class="label label-success">Active</span>
                @else
                    <span class="label label-default">Inactive</span>
                @endif
            </td>
            <td class="text-right">
                {!! Form::open(['route' => ['admin.airlines.destroy', $al->id], 'method' => 'delete']) !!}
                <a href="{!! route('admin.airlines.edit', [$al->id]) !!}"
                   class='btn btn-sm btn-success btn-icon'><i class="fa fa-pencil-square-o"></i></a>
                {!! Form::button('<i class="fa fa-times"></i>', ['type' => 'submit', 'class' => 'btn btn-sm btn-danger btn-icon', 'onclick' => "return confirm('Are you sure?')"]) !!}
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
