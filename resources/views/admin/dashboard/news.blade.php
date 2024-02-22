<div id="pjax_news_wrapper">
  <div class="card border-blue-bottom">
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
            <td colspan="2" class="text-right">
              {{ Form::button('<i class="fas fa-plus-circle"></i>&nbsp;add', ['type' => 'submit', 'class' => 'btn btn-success btn-s']) }}
            </td>
        </table>
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
              <td>
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
</div>
@section('scripts')
  @parent
  <script src="{{ public_asset('assets/vendor/ckeditor4/ckeditor.js') }}"></script>
  <script>
    $(document).ready(function () { CKEDITOR.replace('news_editor'); });
  </script>
@endsection
