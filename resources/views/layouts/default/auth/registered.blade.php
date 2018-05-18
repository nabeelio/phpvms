@extends('app')
@section('title', trans('frontend.auth.registrationsubmitted'))

@section('content')
<div class="container registered-page">
    <h3>@lang('frontend.auth.registrationconfirmation')</h3>
    <p>
        @lang('frontend.auth.confirmationmessage')
    </p>
</div>
@endsection
