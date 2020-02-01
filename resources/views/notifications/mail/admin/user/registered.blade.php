@component('mail::message')
  A new user has signed up!

  Name: {{ $user->name }}!
  Email: {{ $user->email }}
  State: {{ UserState::label($user->state) }}

  {{ config('app.name') }}
@endcomponent
