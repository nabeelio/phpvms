<table class="table table-responsive" id="aircrafts-table">
    <thead>
        <th>Subfleet</th>
        <th>Name</th>
        <th>Registration</th>
        <th style="text-align: center;">Active</th>
        <th style="text-align: center;">Actions</th>
    </thead>
    <tbody>
    @foreach($aircraft as $ac)
        <tr>
            <td>
                @if($ac->subfleet_id)
                    {!! $ac->subfleet->name !!}
                @else
                    -
                @endif
            </td>
            <td><a href="{!! route('admin.aircraft.show', [$ac->id]) !!}">{!! $ac->name !!}</a></td>
            <td>{!! $ac->registration !!}</td>
            <td style="text-align: center;">
                <i class="fa fa-{{$ac->active == 1?"check":""}}-square-o" aria-hidden="true"
                   style="color: {{$ac->active==1?"darkgreen":"darkred"}};font-size:20px;"></i>
            </td>
            <td style="width: 10%; text-align: center;" class="form-inline">
                {!! Form::open(['route' => ['admin.aircraft.destroy', $ac->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('admin.aircraft.show', [$ac->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('admin.aircraft.edit', [$ac->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
