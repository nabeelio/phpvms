@if($user->awards->count() > 0)
  <table class="table table-hover">
  @foreach($user->awards as $award)
    <tr>
      <td>{{ $award->name }}</td>
      <td>{{ $award->description }}</td>
      <td>
        {{ Form::open(['url' => url('/admin/users/'.$user->id.'/award/'.$award->id),
              'method' => 'delete', 'class' => 'pjax_form form-inline']) }}
        {{ Form::button('<i class="fa fa-times"></i>', ['type' => 'submit',
                         'class' => 'btn btn-danger btn-small',
                         'onclick' => "return confirm('Are you sure?')",
                         ]) }}
        {{ Form::close() }}
      </td>
    </tr>
  @endforeach
  </table>
@else
  <div class="jumbotron">
    <p class="text-center">This user has no awards</p>
  </div>
@endif
