<div id="pirep_comments_wrapper" class="col-12">
  <table class="table table-responsive" id="pireps-comments-table">
    <thead>
    <th></th>
    <th></th>
    <th></th>
    </thead>
    <tbody>
    @foreach($pirep->comments as $comment)
      <tr>
        <td width="1%" nowrap="" style="vertical-align: text-top">
          <a href="{{ route('admin.users.show', [$comment->user_id]) }}">
            {{ $comment->user->name }}
          </a>
        </td>
        <td>
          <p>{{ $comment->comment }}</p>
          <p class="small">{{ show_datetime($comment->created_at) }}</p>
        </td>
        <td align="right">
          {{ Form::open(['url' => url('/admin/pireps/'.$pirep->id.'/comments'),
                      'method' => 'delete', 'class' => 'pjax_form form-inline']) }}
          {{ Form::hidden('comment_id', $comment->id) }}
          {{ Form::button('<i class="fa fa-times"></i>', ['type' => 'submit',
                           'class' => 'btn btn-danger btn-small',
                           'onclick' => "return confirm('Are you sure?')",
                           ]) }}
          {{ Form::close() }}
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
  <hr/>
  <div class="row">
    <div class="col-sm-12">
      <div class="text-right">
        {{ Form::open(['url' => url('/admin/pireps/'.$pirep->id.'/comments'),
                        'method' => 'post', 'class' => 'pjax_form form-inline']) }}
        {{ Form::input('text', 'comment', null, ['class' => 'form-control input-sm']) }}
        {{ Form::button('<i class="fa fa-plus"></i> Add', ['type' => 'submit',
                         'class' => 'btn btn-success btn-small']) }}
        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>
