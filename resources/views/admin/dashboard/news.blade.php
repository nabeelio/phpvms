<div id="pjax_news_wrapper">
  <div class="card border-blue-bottom">
    @if($news->count() === 0)
      <div class="text-center text-muted" style="padding: 30px;">
        No news items
      </div>
    @endif
    @foreach($news as $item)
      <div class="content">
        <div class="header">
          <h5 class="title">
            <b>{{ $item->subject }}</b>
          </h5>
          <div class="text-right">
            {{ Form::open(['route' => 'admin.dashboard.news', 'method' => 'delete', 'class' => 'pjax_news_form']) }}
            {{ Form::hidden('news_id', $item->id) }}
            {{ 
              Form::button('<i class="far fa-trash-alt"></i>&nbsp;delete', [
                'type'    => 'submit', 
                'class'   => ' btn btn-danger btn-xs text-small', 
                'onclick' => "return confirm('Are you sure?')"
              ]) 
            }}
            {{ Form::close() }}
          </div>
        </div>
        <div class="body">
          {!! $item->body !!}
        </div>
        <div class="footer text-right">
          {{ $item->user->name }} - {{ show_datetime($item->created_at) }}
        </div>
      </div>
      <hr/>
    @endforeach
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
      </table>
      <div class="text-right">
        {{ Form::button('<i class="fas fa-plus-circle"></i>&nbsp;add', ['type' => 'submit', 'class' => 'btn btn-success btn-s']) }}
        {{ Form::close() }}
      </div>
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
