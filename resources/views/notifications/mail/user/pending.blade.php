@component('mail::message')
  # Thanks for signing up, {{ $user->name }}!

  You will be notified as soon as your account is approved!

  Thanks,<br>
  Management, {{ config('app.name') }}
@endcomponent
