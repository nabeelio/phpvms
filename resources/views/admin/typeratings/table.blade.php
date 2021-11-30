<div id="typeratings_table_wrapper">
  <table class="table table-hover table-responsive">
    <thead>
      <th>Type Code</th>
      <th>Name</th>
      <th>Description</th>
      <th></th>
    </thead>
    <tbody>
      @foreach($typeratings as $typerating)
        <tr>
          <td><a href="{{ route('admin.typeratings.edit', [$typerating->id]) }}">{{ $typerating->type }}</a></td>
          <td>{{ $typerating->name }}</td>
          <td>{{ $typerating->description }}</td>
          <td class="text-right">
            {{ Form::open(['route' => ['admin.typeratings.destroy', $typerating->id], 'method' => 'delete']) }}
            <a href="{{ route('admin.typeratings.edit', [$typerating->id]) }}" class='btn btn-sm btn-success btn-icon'>
              <i class="fas fa-pencil-alt"></i></a>
            {{ Form::button('<i class="fa fa-times"></i>', ['type' => 'submit', 'class' => 'btn btn-sm btn-danger btn-icon', 'onclick' => "return confirm('Are you sure?')"]) }}
            {{ Form::close() }}
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
