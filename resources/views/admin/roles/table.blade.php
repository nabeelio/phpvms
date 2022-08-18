<table class="table table-hover table-responsive" id="roles-table">
  <thead>
  <th>Name</th>
  <th class="text-center">Members</th>
  <th class="text-right">Actions</th>
  </thead>
  <tbody>
  @foreach($roles as $role)
    <tr>
      <td>{{ $role->display_name }}</td>
      <td class="text-center">{{ $role->users_count }}</td>
      <td class="text-right">
        {{ Form::open(['route' => ['admin.roles.destroy', $role->id], 'method' => 'delete']) }}
        <a href="{{ route('admin.roles.edit', [$role->id]) }}"
           class='btn btn-sm btn-success btn-icon'><i class="fas fa-pencil-alt"></i></a>
        {{ Form::button('<i class="fa fa-times"></i>', ['type' => 'submit', 'class' => 'btn btn-sm btn-danger btn-icon', 'onclick' => "return confirm('Are you sure?')"]) }}
        {{ Form::close() }}
      </td>
    </tr>
  @endforeach
  </tbody>
</table>
