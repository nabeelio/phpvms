<table class="table table-hover table-responsive" id="aircrafts-table">
    <thead>
        <th>Subfleet</th>
        <th>Name</th>
        <th>Registration</th>
        <th style="text-align: center;">Active</th>
        <th style="text-align: right;"></th>
    </thead>
    <tbody>
    @foreach($aircraft as $ac)
        <tr>
            <td>
                @if($ac->subfleet_id)
                    <a href="{!! route('admin.subfleets.edit', [$ac->subfleet_id]) !!}">
                    {!! $ac->subfleet->name !!}
                    </a>
                @else
                    -
                @endif
            </td>
            <td><a href="{!! route('admin.aircraft.show', [$ac->id]) !!}">{!! $ac->name !!}</a></td>
            <td>{!! $ac->registration !!}</td>
            <td style="text-align: center;">
                @if($ac->active == 1)
                    <span class="label label-success">Active</span>
                @else
                    <span class="label label-default">Inactive</span>
                @endif
            </td>
            <td style="width: 10%; text-align: right;">
                {!! Form::open(['route' => ['admin.aircraft.destroy', $ac->id], 'method' => 'delete']) !!}
                <a href="{!! route('admin.aircraft.edit', [$ac->id]) !!}" class='btn btn-sm btn-success btn-icon'>
                    <i class="fa fa-pencil-square-o"></i>
                </a>
                {!! Form::button('<i class="fa fa-times"></i>', ['type' => 'submit', 'class' => 'btn btn-sm btn-danger btn-icon', 'onclick' => "return confirm('Are you sure?')"]) !!}
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
