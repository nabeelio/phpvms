<table class="table table-responsive" id="flights-table">
    <thead>
        <th>Flight #</th>
        <th>Dep</th>
        <th>Arr</th>
        <th>Route</th>
        <th>Dpt Time</th>
        <th>Arr Time</th>
        <th>Notes</th>
        <th>Active</th>
        <th colspan="3">Action</th>
    </thead>
    <tbody>
    @foreach($flights as $flight)
        <tr>
            <td>
                <a href="{!! route('admin.flights.show', [$flight->id]) !!}">
                {!! $flight->airline->code !!}{!! $flight->flight_number !!}
                @if($flight->route_code)
                    (C: {!! $flight->route_code !!} L: {!! $flight->route_leg !!})
                @endif
                </a>
            </td>
            <td>{!! $flight->dpt_airport->icao !!}</td>
            <td>
                {!! $flight->arr_airport->icao !!}
                @if($flight->alt_airport)
                    (Alt: {!! $flight->alt_airport->icao !!})
                @endif
            </td>
            <td>{!! $flight->route !!}</td>
            <td>{!! $flight->dpt_time !!}</td>
            <td>{!! $flight->arr_time !!}</td>
            <td>{!! $flight->notes !!}</td>
            <td>{!! $flight->active !!}</td>
            <td>
                {!! Form::open(['route' => ['admin.flights.destroy', $flight->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('admin.flights.show', [$flight->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('admin.flights.edit', [$flight->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
