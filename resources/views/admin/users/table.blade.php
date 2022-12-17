<table class="table table-hover table-responsive" id="users-table">
  <thead>
  <th>ID</th>
  <th>Callsign</th>
  <th>Name</th>
  <th>Email</th>
  <th>Registered</th>
  <th class="text-center">Active</th>
  <th></th>
  </thead>
  <tbody>
  @foreach($users as $user)
    <tr>
      <td>{{ $user->ident }}</td>
      <td>{{ $user->callsign }}</td>
      <td>
        @if(filled($user->country))
          <span class="flag-icon flag-icon-{{ $user->country }}" title="{{ $country->alpha2($user->country)['name'] }}"></span>&nbsp;
        @endif
        <a href="{{ route('admin.users.edit', [$user->id]) }}">{{ $user->name }}</a>
      </td>
      <td>{{ $user->email }}</td>
      <td>{{ show_date($user->created_at) }}</td>
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
