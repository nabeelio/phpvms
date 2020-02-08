@foreach (session('flash_notification', collect())->toArray() as $message)
  <div class="alert alert-danger" role="alert">
    <div class="container">
      <div class="alert-icon">
        <i class="now-ui-icons ui-2_like"></i>
      </div>
      {{ $message['message'] }}
    </div>
  </div>
@endforeach
{{ session()->forget('flash_notification') }}
