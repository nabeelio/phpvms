@component('mail::message')
A new user has signed up!

Name: {!! $user->name !!}!
Email: {!! $user->email !!}
State: {!! PilotState::label($user->state) !!}

{{ config('app.name') }}
@endcomponent
