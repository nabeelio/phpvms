<div class="content table-responsive table-full-width">
<table class="table table-hover table-striped" id="flights-table">
    <thead>
        <th>Flight #</th>
        <th>Dep</th>
        <th>Arr</th>
        <th>Route</th>
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
            <td>{!! $flight->route !!}</td>
            <td>{!! $flight->dpt_time !!}</td>
            <td>{!! $flight->arr_time !!}</td>
            <td>{!! $flight->notes !!}</td>
            <td style="text-align: center;">
                <i class="fa fa-{{$flight->active == 1?"check":""}}-square-o" aria-hidden="true"
                   style="color: {{$flight->active==1?"darkgreen":"darkred"}};font-size:20px;"></i>
            </td>
            <td style="text-align: right;">
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
</div>
