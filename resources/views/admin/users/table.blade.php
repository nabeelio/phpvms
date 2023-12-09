<table class="table table-hover table-responsive" id="users-table">
  <thead>
  <th>@sortablelink('id', 'ID')</th>
  <th>@sortablelink('pilot_id', 'Ident')</th>
  <th>@sortablelink('callsign', 'Callsign')</th>
  <th>@sortablelink('country', 'Country')</th>
  <th>@sortablelink('name', 'Name')</th>
  <th>@sortablelink('email', 'E-Mail')</th>
  {{-- <th class="text-center">@sortablelink('home_airport_id', 'Home Airport')</th> --}}
  {{-- <th class="text-center">@sortablelink('curr_airport_id', 'Curr Airport')</th> --}}
  <th class="text-center">@sortablelink('flights', 'Flights')</th>
  <th class="text-center">@sortablelink('flight_time', 'Flight Time')</th>
  <th class="text-center">@sortablelink('transfer_time', 'Transfer Hours')</th>
  <th class="text-center">@sortablelink('created_at', 'Registered')</th>
  <th class="text-center">@sortablelink('state', 'State')</th>
  <th class="text-center">Actions</th>
  </thead>
  <tbody>
  @foreach($users as $user)
    <tr>
      <td>
        <a href="{{ route('admin.users.edit', [$user->id]) }}">{{ $user->id }}</a>
      </td>
      <td>{{ $user->pilot_id }}</td>
      <td>{{ $user->callsign }}</td>
      <td>
        @if(filled($user->country))
          <span class="flag-icon flag-icon-{{ $user->country }}" title="{{ $country->alpha2($user->country)['name'] }}"></span>
        @endif
      </td>
      <td>
        <a href="{{ route('admin.users.edit', [$user->id]) }}">{{ $user->name }}</a>
      </td>
      <td>{{ $user->email }}</td>
      {{-- <td class="text-center">{{ $user->home_airport_id }}</td> --}}
      {{-- <td class="text-center">{{ $user->curr_airport_id }}</td> --}}
      <td class="text-center">{{ $user->flights }}</td>
      <td class="text-center">@minutestotime($user->flight_time)</td>
      <td class="text-center">@minutestohours($user->transfer_time)</td>
      <td class="text-center">{{ show_date($user->created_at) }}</td>
      <td class="text-center">
        @if($user->state === UserState::ACTIVE)
          <span class="label label-success">
        @elseif($user->state === UserState::PENDING)
          <span class="label label-warning">
        @else
          <span class="label label-default">
        @endif
        {{ UserState::label($user->state) }}</span>
      </td>
      <td class="text-right">
        {{ Form::open(['route' => ['admin.users.destroy', $user->id], 'method' => 'delete']) }}
        <a href="{{ route('admin.users.edit', [$user->id]) }}" class='btn btn-sm btn-success btn-icon'>
          <i class="fas fa-pencil-alt"></i>
        </a>
        {{ Form::button('<i class="fa fa-times"></i>', ['type' => 'submit', 'class' => 'btn btn-sm btn-danger btn-icon', 'onclick' => "return confirm('Are you sure?')"]) }}
        {{ Form::close() }}
      </td>
    </tr>
  @endforeach
  </tbody>
</table>
