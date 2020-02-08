@component('mail::message')
  # Hi {{ $user->name }},

  Your registration to our airline was denied. Please contact
  an administrator with any questions you may have.

  Thanks,<br>
  Management, {{ config('app.name') }}
@endcomponent
