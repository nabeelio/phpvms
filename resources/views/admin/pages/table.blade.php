<table class="table table-hover table-responsive" id="pages-table">
  <thead>
  <th>Name</th>
  <th></th>
  </thead>
  <tbody>
  @foreach($pages as $page)
    <tr>
      <td>{{ $page->name }}</td>
      <td class="text-right">
        {{ Form::open(['route' => ['admin.pages.destroy', $page->id], 'method' => 'delete']) }}
        <a href="{{ route('admin.pages.edit', [$page->id]) }}"
           class='btn btn-sm btn-success btn-icon'><i class="fas fa-pencil-alt"></i></a>
        {{ Form::button('<i class="fa fa-times"></i>', ['type' => 'submit', 'class' => 'btn btn-sm btn-danger btn-icon', 'onclick' => "return confirm('Are you sure?')"]) }}
        {{ Form::close() }}
      </td>
    </tr>
  @endforeach
  </tbody>
</table>
