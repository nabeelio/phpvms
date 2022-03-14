@component('mail::message')
  # {{ $news->subject }}

  {!! $news->body !!}

  Thanks,<br>
  {{ config('app.name') }}
@endcomponent
