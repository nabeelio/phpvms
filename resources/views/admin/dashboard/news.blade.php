<div id="pjax_news_wrapper">
  <div class="card border-blue-bottom">
    @if($news->count() === 0)
      <div class="text-center text-muted" style="padding: 30px;">
        no news items
      </div>
    @endif
    @foreach($news as $item)
      <div class="content">
        <div class="header">
          <h4 class="title">{{ $item->subject }}</h4>
          <p class="category">{{ $item->user->name }} - {{ show_datetime($item->created_at) }}</p>
        </div>

        {{ $item->body }}

        <div class="text-right">
          {{ Form::open(['route' => 'admin.dashboard.news',
          'method' => 'delete',
          'class' => 'pjax_news_form',
      ]) }}
          {{ Form::hidden('news_id', $item->id) }}
          {{
               Form::button('<i class="far fa-trash-alt"></i>&nbsp;delete',
                               ['type' => 'submit',
                                'class' => ' btn btn-danger btn-xs text-small',
                                'onclick' => "return confirm('Are you sure?')"
                                ])

               }}
          {{ Form::close() }}
        </div>
      </div>
      <hr/>
    @endforeach
    <div class="content">
      <div class="header">
        <h4 class="title">Add News</h4>
      </div>
      {{ Form::open(['route' => 'admin.dashboard.news',
          'method' => 'post',
          'class' => 'pjax_news_form',
      ]) }}
      <table class="table">
        <tr>
          <td>{{ Form::label('subject', 'Subject:') }}</td>
          <td>{{ Form::text('subject', '', ['class' => 'form-control'])  }}</td>
        </tr>

        <tr>
          <td>{{ Form::label('body', 'Body:') }}</td>
          <td>{{ Form::textarea('body', '', ['class' => 'form-control']) }}</td>
        </tr>
      </table>
      <div class="text-right">
        {{
         Form::button('<i class="fas fa-plus-circle"></i>&nbsp;add',
                         ['type' => 'submit',
                          'class' => 'btn btn-success btn-s'])

         }}
        {{ Form::close() }}
      </div>
    </div>
  </div>
</div>
