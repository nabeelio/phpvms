<div class="content table-responsive table-full-width">
<table class="table table-hover" id="flights-table">
    <thead>
        <th>Flight #</th>
        <th>Dep</th>
        <th>Arr</th>
        {{--<th>Route</th>--}}
        <th>Dpt Time</th>
        <th>Arr Time</th>
        <th>Notes</th>
        <th style="text-align: center;">Active</th>
        <th colspan="3" style="text-align: right;">Action</th>
    </thead>
    <tbody>
    @foreach($flights as $flight)
        <tr>
            <td>
                <a href="{!! route('admin.flights.edit', [$flight->id]) !!}">
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
            {{--<td>{!! $flight->route !!}</td>--}}
            <td>{!! $flight->dpt_time !!}</td>
            <td>{!! $flight->arr_time !!}</td>
            <td>{!! $flight->notes !!}</td>
            <td style="text-align: center;">
                @if($flight->active == 1)
                    <span class="label label-success">Active</span>
                @else
                    <span class="label label-default">Inactive</span>
                @endif
            </td>
            <td style="text-align: right;">
                {!! Form::open(['route' => ['admin.flights.destroy', $flight->id], 'method' => 'delete']) !!}
                <a href="{!! route('admin.flights.edit', [$flight->id]) !!}" class='btn btn-sm btn-success btn-icon'><i class="fas fa-pencil-alt"></i></a>
                {!! Form::button('<i class="fa fa-times"></i>', ['type' => 'submit', 'class' => 'btn btn-sm btn-danger btn-icon', 'onclick' => "return confirm('Are you sure?')"]) !!}
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
