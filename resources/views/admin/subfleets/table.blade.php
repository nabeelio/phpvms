<table class="table table-hover table-responsive" id="subfleets-table">
    <thead>
        <th>Airline</th>
        <th>Name</th>
        <th>Type</th>
        <th>Fuel Type</th>
        <th></th>
    </thead>
    <tbody>
    @foreach($subfleets as $subfleet)
        <tr>
            <td>{!! $subfleet->airline->name !!}</td>
            <td>{!! $subfleet->name !!}</td>
            <td>{!! $subfleet->type !!}</td>
            <td>
                @if($subfleet->fuel_type === config('enums.fuel_types.100LL'))
                    100LL
                @elseif($subfleet->fuel_type === config('enums.fuel_types.JETA'))
                    JETA
                @elseif($subfleet->fuel_type === config('enums.fuel_types.MOGAS'))
                    MOGAS
                @else
                    -
                @endif
            </td>
            <td class="text-right">
                {!! Form::open(['route' => ['admin.subfleets.destroy', $subfleet->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('admin.subfleets.show', [$subfleet->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('admin.subfleets.edit', [$subfleet->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
