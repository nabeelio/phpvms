<div id="pjax_news_wrapper">
  <div class="card border-blue-bottom" id="add_news">
    <div class="content">
      <div class="header">
        <h4 class="title">Add News</h4>
      </div>
      {{ Form::open(['route' => 'admin.dashboard.news', 'method' => 'post', 'class' => 'pjax_news_form']) }}
        <table class="table">
          <tr>
            <td>{{ Form::label('subject', 'Subject:') }}</td>
            <td>{{ Form::text('subject', '', ['class' => 'form-control'])  }}</td>
          </tr>
          <tr>
            <td>{{ Form::label('body', 'Body:') }}</td>
            <td>{!! Form::textarea('body', '', ['id' => 'news_editor', 'class' => 'editor']) !!}</td>
          </tr>
          <tr>
        </table>
        <div style="display:flex; align-items: center; justify-content: space-between;">
          <div class="checkbox">
            <label class="checkbox-inline">
              {{ Form::label('send_notifications', 'Send notifications:') }}
              <input name="send_notifications" type="hidden" value="0"/>
              {{ Form::checkbox('send_notifications') }}
            </label>
          </div>
          <div>
            {{ Form::button('<i class="fas fa-plus-circle"></i>&nbsp;add', ['type' => 'submit', 'class' => 'btn btn-success btn-s']) }}
          </div>
        </div>
      {{ Form::close() }}
    </div>
  </div>
  <div class="card border-blue-bottom" id="edit_news" style="display:none;">
    <div class="content">
      <div class="header">
        <h4 class="title" id="edit_title">Edit News</h4>
      </div>
      {{ Form::open(['route' => 'admin.dashboard.news', 'method' => 'patch', 'class' => 'pjax_news_form']) }}
        {{ Form::hidden('id', '', ['id' => 'edit_id']) }}
        <table class="table">
          <tr>
            <td>{{ Form::label('subject', 'Subject:') }}</td>
            <td>{{ Form::text('subject', '', ['id' => 'edit_subject', 'class' => 'form-control'])  }}</td>
          </tr>
          <tr>
            <td>{{ Form::label('body', 'Body:') }}</td>
            <td>{!! Form::textarea('body', '', ['id' => 'edit_body', 'class' => 'editor']) !!}</td>
          </tr>
        </table>
        <div style="display:flex; align-items: center; justify-content: space-between;">
          <div class="checkbox">
            <label class="checkbox-inline">
              {{ Form::label('send_notifications', 'Send notifications:') }}
              <input name="send_notifications" type="hidden" value="0"/>
              {{ Form::checkbox('send_notifications') }}
            </label>
          </div>
          <div>
            <button type="button" class="btn btn-warning btn-s" onclick="closeEdit()">Cancel</button>
            {{ Form::button('<i class="fas fa-pencil-alt"></i>&nbsp;edit', ['type' => 'submit', 'class' => 'btn btn-success btn-s']) }}
          </div>
        </div>
      {{ Form::close() }}
    </div>
  </div>
  <div class="card border-blue-bottom">
    <div class="content">
      @if($news->count() === 0)
        <div class="text-center text-muted" style="padding: 30px;">
          No news items
        </div>
      @else
        <table class="table">
          <tr>
            <th>Subject</th>
            <th>Body</th>
            <th>Poster</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
          @foreach($news as $item)
            <tr>
              <th>{{ $item->subject }}</th>
              <td>{!! $item->body!!}</td>
              <td>{{ optional($item->user)->name_private }}</td>
              <td>{{ $item->created_at->format('d.M.y') }}</td>
              <td style="display: flex;gap: .5rem;">
                <button class="btn btn-primary btn-xs text-small" onclick="editNews({{ $item->toJson() }})">Edit</button>
                {{ Form::open(['route' => 'admin.dashboard.news', 'method' => 'delete', 'class' => 'pjax_news_form']) }}
                {{ Form::hidden('news_id', $item->id) }}
                {{ Form::button('Delete', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs text-small', 'onclick' => "return confirm('Are you sure?')"]) }}
                {{ Form::close() }}
              </td>
            </tr>
          @endforeach
        </table>
      @endif
    </div>
  </div>
  <script>
    $(document).ready(function () { CKEDITOR.replace('news_editor'); });
    if (typeof $('input').iCheck !== 'undefined') {
      $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'icheckbox_square-blue'
      });
    }
  </script>
</div>
@section('scripts')
  @parent
  <script src="{{ public_asset('assets/vendor/ckeditor4/ckeditor.js') }}"></script>
  <script>
    function editNews(news) {
      CKEDITOR.replace('edit_body')
      $('#edit_title').html('Edit News: ' + news.subject);
      $('#edit_subject').val(news.subject)
      CKEDITOR.instances.edit_body.setData(news.body)
      $('#edit_id').val(news.id)
      $('#add_news').hide();
      $('#edit_news').show();
    }

    function closeEdit() {
      $('#edit_news').hide();
      $('#add_news').show()
    }

  </script>
@endsection
