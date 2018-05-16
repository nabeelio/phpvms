@extends('app')
@section('title', __('Registration Submitted'))

@section('content')
<div class="container registered-page">
    <h3>{{ __('Registration Confirmation') }}</h3>
    <p>
        __('Your application has been submitted. It requires staff member approval, once a\nstaff member has reviewed your application, you will receive a confirmation email.') }}
    </p>
</div>
@endsection
