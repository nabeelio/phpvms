<table class="table table-responsive" id="airports-table">
    <thead>
        <th>ICAO</th>
        <th>Name</th>
        <th>Location</th>
        <th colspan="3">Action</th>
    </thead>
    <tbody>
    @foreach($airports as $airport)
        <tr>
            <td>{!! $airport->icao !!}</td>
            <td>{!! $airport->name !!}</td>
            <td>{!! $airport->location !!} ({!! $airport->lat !!}x{!! $airport->lon !!})</td>
            <td>
                {!! Form::open(['route' => ['admin.airports.destroy', $airport->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('admin.airports.show', [$airport->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('admin.airports.edit', [$airport->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
