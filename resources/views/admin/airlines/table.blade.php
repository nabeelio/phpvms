<table class="table table-hover table-responsive text-center" id="airlines-table">
  <thead>
    <th class="text-left">@sortablelink('name', 'Company Name')</th>
    <th class="text-center">@sortablelink('country', 'Country')</th>
    <th class="text-center">@sortablelink('iata', 'IATA Code')</th>
    <th class="text-center">@sortablelink('icao', 'ICAO Code')</th>
    <th class="text-center">@sortablelink('callsign', 'Radio Callsign')</th>
    <th class="text-center">Active</th>
    <th class="text-right">Actions</th>
  </thead>
  <tbody>
    @foreach($airlines as $al)
      <tr>
        <td class="text-left">
          <a href="{{ route('admin.airlines.edit', [$al->id]) }}">{{ $al->name }}</a>
        </td>
        <td nowrap="true">
          @if(filled($al->country))
            <span class="flag-icon flag-icon-{{ $al->country }}" title="{{ $country->alpha2($al->country)['name'] }}"></span>
          @endif
        </td>
        <td>{{ $al->iata }}</td>
        <td>{{ $al->icao }}</td>
        <td>{{ $al->callsign }}</td>
        <td>
          @if($al->active == 1)
            <span class="label label-success">Active</span>
          @else
            <span class="label label-default">Inactive</span>
          @endif
        </td>
        <td class="text-right">
          {{ Form::open(['route' => ['admin.airlines.destroy', $al->id], 'method' => 'delete']) }}
          <a href="{{ route('admin.airlines.edit', [$al->id]) }}"
            class='btn btn-sm btn-success btn-icon'><i class="fas fa-pencil-alt"></i></a>
          {{ Form::button('<i class="fa fa-times"></i>', ['type' => 'submit', 'class' => 'btn btn-sm btn-danger btn-icon', 'onclick' => "return confirm('Are you sure?')"]) }}
          {{ Form::close() }}
        </td>
      </tr>
    @endforeach
  </tbody>
</table>
