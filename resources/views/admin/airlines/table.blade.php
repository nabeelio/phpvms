<table class="table table-responsive" id="airlines-table">
    <thead>
        <th>Code</th>
        <th>IATA</th>
        <th>Name</th>
        <th style="text-align: center;">Active</th>
        <th colspan="3" style="text-align: right;">Action</th>
    </thead>
    <tbody>
    @foreach($airlines as $al)
        <tr>
            <td>{!! $al->code !!}</td>
            <td>{!! $al->iata !!}</td>
            <td>{!! $al->name !!}</td>
            <td style="text-align: center;">
                @if($al->active == 1)
                    <span class="label label-success">Active</span>
                @else
                    <span class="label label-default">Inactive</span>
                @endif
            </td>
            <td style="text-align: right;">
                {!! Form::open(['route' => ['admin.airlines.destroy', $al->id], 'method' => 'delete']) !!}
                {{--<a href="{!! route('admin.airlines.show', [$al->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>--}}
                <a href="{!! route('admin.airlines.edit', [$al->id]) !!}" class='btn btn-sm btn-success btn-icon'>
                    <i class="fa fa-pencil-square-o"></i></a>
                {!! Form::button('<i class="fa fa-times"></i>', ['type' => 'submit', 'class' => 'btn btn-sm btn-danger btn-icon', 'onclick' => "return confirm('Are you sure?')"]) !!}
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
