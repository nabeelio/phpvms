<div class="nav nav-tabs" role="tablist" style="background: #067ec1; color: #FFF;">
    News
</div>
<div class="card border-blue-bottom">
    <div class="card-block" style="min-height: 0px">
        @if($news->count() === 0)
            <div class="text-center text-muted" style="padding: 30px;">
                no news items
            </div>
        @endif

        @foreach($news as $item)
            <h4 style="margin-top: 0px;">{{ $item->subject }}</h4>
            <p class="category">{{ $item->user->name }}
                - {{ show_datetime($item->created_at) }}</p>

            {{ $item->body }}
        @endforeach
    </div>
</div>
