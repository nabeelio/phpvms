@foreach (session('flash_notification', collect())->toArray() as $message)
    @if ($message['overlay'])
        @include('flash::modal', [
            'modalClass' => 'flash-modal',
            'title'      => $message['title'],
            'body'       => $message['message']
        ])
    @else
        <div class="alert
                    alert-{{ $message['level'] }}
        {{ $message['important'] ? 'alert-important' : '' }}"
             role="alert"
        >
            <div class="alert-icon">
                <i class="now-ui-icons ui-2_like"></i>
            </div>
            @if ($message['important'])
            @endif
            <button type="button"
                    class="close"
                    data-dismiss="alert"
                    aria-hidden="true">&times;</button>

            {{ $message['message'] }}

        </div>
    @endif
@endforeach

{{ session()->forget('flash_notification') }}
