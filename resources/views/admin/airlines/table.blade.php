<table class="table table-responsive" id="airlines-table">
    <thead>
        <th>Code</th>
        <th>Name</th>
        <th style="text-align: center;">Active</th>
        <th colspan="3" style="text-align: right;">Action</th>
    </thead>
    <tbody>
    @foreach($airlines as $al)
        <tr>
            <td>{!! $al->code !!}</td>
            <td>{!! $al->name !!}</td>
            <td style="text-align: center;">
                <i class="fa fa-{{$al->active == 1?"check":""}}-square-o" aria-hidden="true"
                   style="color: {{$al->active==1?"darkgreen":"darkred"}};font-size:20px;"></i>
            </td>
            <td style="text-align: right;">
                {!! Form::open(['route' => ['admin.airlines.destroy', $al->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('admin.airlines.show', [$al->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('admin.airlines.edit', [$al->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
