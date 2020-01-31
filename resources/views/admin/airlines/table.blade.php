<table class="table table-hover table-responsive" id="airlines-table">
  <thead>
  <th>Code</th>
  <th>Name</th>
  <th class="text-center">Active</th>
  <th></th>
  </thead>
  <tbody>
  @foreach($airlines as $al)
    <tr>
      <td nowrap="true">
        @if(filled($al->country))
          <span class="flag-icon flag-icon-{{ $al->country }}"></span>
          &nbsp;
        @endif
        <a href="{{ route('admin.airlines.edit', [$al->id]) }}">{{ $al->iata }}/{{ $al->icao }}</a>
      </td>
      <td>{{ $al->name }}</td>
      <td class="text-center">
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
