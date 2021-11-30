<div id="user_typeratings_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
  <table id="user_typeratings" class="table table-hover">
    @if(count($user->typeratings))
      <thead>
        <tr>
          <th>Type</th>
          <th>Name</th>
          <th></th>
        </tr>
      </thead>
    @endif
    <tbody>
      @foreach($user->typeratings as $tr)
        <tr>
          <td class="sorting_1">{{ $tr->type }}</td>
          <td>{{ $tr->name }}</td>
          <td style="text-align: right; width:3%;">
            {{ Form::open(['url' => '/admin/users/'.$user->id.'/typeratings', 'method' => 'delete', 'class' => 'modify_typerating']) }}
            {{ Form::hidden('typerating_id', $tr->id) }}
            {{ Form::button('<i class="fa fa-times"></i>', ['type' => 'submit', 'class' => 'btn btn-sm btn-danger btn-icon']) }}
            {{ Form::close() }}
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <hr/>
  <div class="row">
    <div class="col-xs-12">
      <div class="text-right">
        {{ Form::open(['url' => '/admin/users/'.$user->id.'/typeratings', 'method' => 'post', 'class' => 'modify_typerating form-inline']) }}
        {{ Form::select('typerating_id', $avail_ratings, null, ['placeholder' => 'Select Type Rating', 'class' => 'ac-fare-dropdown form-control input-lg select2']) }}
        {{ Form::button('<i class="glyphicon glyphicon-plus"></i> add', ['type' => 'submit', 'class' => 'btn btn-success btn-s']) }}
        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>