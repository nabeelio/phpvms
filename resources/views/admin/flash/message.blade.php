@foreach ($errors->all() as $message)
<div class="alert alert-danger" role="alert">
    <div class="container">
        <div class="alert-icon">
            <i class="now-ui-icons ui-2_like"></i>
        </div>
        {!! $message !!}
    </div>
</div>
@endforeach
@if (session()->has('flash_notification.message'))
    <div class="alert
                alert-{{ session('flash_notification.level') }}
    {{ session()->has('flash_notification.important') ? 'alert-important' : '' }}">
        @if(session()->has('flash_notification.important'))
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        @endif

            <div class="alert alert-danger" role="alert">
                <div class="container">
                    <div class="alert-icon">
                        <i class="now-ui-icons ui-2_like"></i>
                    </div>
        {!! session('flash_notification.message') !!}
                </div>
            </div>
        </div>
    </div>
@endif
{{ session()->forget('flash_notification') }}
