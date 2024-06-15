<div id="airlineNews" class="bg-white shadow-sm mt-8">
  <div id="airlineNews_head" class="p-4 border-b border-gray-100">
    <h2 class="text-xl font-medium">Latest news</h2>
    <h6 class="text-sm text-gray-500">Get the latest news from our Virtual Airline</h6>
  </div>
  <div id="airlineNews_body" class="divide-y divide-gray-100">
    @if($news->count() === 0)
      <div class="flex justify-center p-4">
        <span>@lang('widgets.latestnews.nonewsfound')</span>
      </div>
    @endif

    @foreach($news as $item)
      <div class="p-4">
        <h2 class="text-xl font-medium">{{ $item->subject }}</h2>
        <h6 class="text-sm text-gray-500">Posted on {{ show_datetime($item->created_at) }}</h6>
        {!! $item->body !!}
      </div>
    @endforeach
  </div>
</div>
