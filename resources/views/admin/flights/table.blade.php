<div class="content table-responsive table-full-width">
  <table class="table table-hover" id="flights-table">
    <thead>
      {{-- <th>Airline</th> --}}
      <th>@sortablelink('flight_number', 'Flight #')</th>
      <th>@sortablelink('callsign', 'Callsign')</th>
      <th>@sortablelink('dep_airport_id', 'Orig')</th>
      <th>@sortablelink('arr_airport_id', 'Dest')</th>
      <th>@sortablelink('alt_airport_id', 'Altn')</th>
      <th>@sortablelink('dpt_time', 'Dpt Time')</th>
      <th>@sortablelink('arr_time', 'Arr Time')</th>
      <th class="text-center">@sortablelink('subfleets_count', 'Subfleets')</th>
      <th class="text-center">@sortablelink('route', 'Route')</th>
      <th class="text-center">@sortablelink('notes', 'Notes')</th>
      <th class="text-center">@sortablelink('distance', 'Distance')</th>
      <th class="text-center">@sortablelink('flight_time', 'Duration')</th>
      <th class="text-center">@sortablelink('flight_type', 'Type')</th>
      <th class="text-center">@sortablelink('active', 'Active')</th>
      <th class="text-center">@sortablelink('visible', 'Visible')</th>
      <th class="text-right">Actions</th>
    </thead>
    <tbody>
      @foreach($flights as $flight)
        <tr>
          {{-- <td>{{ optional($flight->airline)->name }}</td> --}}
          <td><a href="{{ route('admin.flights.edit', [$flight->id]) }}">{{$flight->ident}}</a></td>
          <td>{{ $flight->callsign }}</td>
          <td>{{ $flight->dpt_airport_id }}</td>
          <td>{{ $flight->arr_airport_id }}</td>
          <td>{{ $flight->alt_airport_id }}</td>
          <td>{{ $flight->dpt_time }}</td>
          <td>{{ $flight->arr_time }}</td>
          <td class="text-center">
            @if($flight->subfleets_count > 0)
              {{ $flight->subfleets_count }}
              <span class="text-info"><i class="fas fa-info-circle fa2x ml-1" title="@foreach($flight->subfleets as $sf){{ $sf->type }} @if(!$loop->last) | @endif @endforeach"></i></span>
            @else
              -
            @endif
          </td>
          <td class="text-center">
            @if(filled($flight->route))
              <span class="text-info"><i class="fas fa-info-circle fa2x" title="{{ $flight->route }}"></i></span>
            @endif
          </td>
          <td class="text-center">
            @if(filled($flight->notes))
              <span class="text-info"><i class="fas fa-info-circle fa2x" title="{{ $flight->notes }}"></i></span>
            @endif
          </td>
          <td class="text-center">{{ round($flight->distance->local()).' '.setting('units.distance') }}</td>
          <td class="text-center">@minutestotime($flight->flight_time)</td>
          <td class="text-center">{{ $flight->flight_type }}</td>
          <td class="text-center">
            @if($flight->active == 1)
              <span class="label label-success">@lang('common.active')</span>
            @else
              <span class="label label-default">@lang('common.inactive')</span>
            @endif
          </td>
          <td class="text-center">
            @if($flight->visible == 1)
              <span class="text-success"><i class="fas fa-check fa2x" title="Visible"></i></span>
            @else
              <span class="text-danger"><i class="fas fa-times fa2x" title="Hidden"></i></span>
            @endif
          </td>
          <td class="text-right">
            {{ Form::open(['route' => ['admin.flights.destroy', $flight->id], 'method' => 'delete']) }}
            <a href="{{ route('admin.flights.edit', [$flight->id]) }}" class='btn btn-sm btn-success btn-icon'><i class="fas fa-pencil-alt"></i></a>
            {{ Form::button('<i class="fa fa-times"></i>', ['type' => 'submit', 'class' => 'btn btn-sm btn-danger btn-icon', 'onclick' => "return confirm('Are you sure?')"]) }}
            {{ Form::close() }}
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
