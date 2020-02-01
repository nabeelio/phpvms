@component('mail::message')
  Your new login details for {{ config('app.name') }} follow:

  Do not share this information with anyone else! <br/>
  <strong>E-Mail Address:</strong> {{ $user->email }}<br/>
  <strong>Temporary Password:</strong> {{ $newpw }}<br/><br/>

  Your account is now ready for use.<br/>
  Upon first login, please reset your password.

  @component('mail::button', ['url' => url('/login')])
    Login & Reset Password
  @endcomponent

  Thanks,<br/>
  Management, {{ config('app.name') }}
@endcomponent
