<div class="content table-responsive table-full-width">

  <div class="header">
    @component('admin.components.info')
      These are custom fields that can be filled out by a user
    @endcomponent
  </div>

  <table class="table table-hover table-responsive" id="pirepFields-table">
    <thead>
    <th>Name</th>
    <th></th>
    </thead>
    <tbody>
    @foreach($fields as $field)
      <tr>
        <td>{{ $field->name }}</td>
        <td class="text-right">
          {{ Form::open(['route' => ['admin.userfields.destroy', $field->id], 'method' => 'delete']) }}
          <a href="{{ route('admin.userfields.edit', [$field->id]) }}"
             class='btn btn-sm btn-success btn-icon'>
            <i class="fas fa-pencil-alt"></i></a>

          {{ Form::button('<i class="fa fa-times"></i>',
                       ['type' => 'submit', 'class' => 'btn btn-sm btn-danger btn-icon',
                        'onclick' => "return confirm('Are you sure?')"]) }}
          {{ Form::close() }}
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
</div>
