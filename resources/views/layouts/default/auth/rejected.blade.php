@extends('app')
@section('title', __('Registration Denied'))

@section('content')
<div class="row">
    <div class="col-md-12 " style="text-align: center;">
        <div class="flex-center position-ref full-height">
            <div class="title m-b-md">
                <h2 class="description">
				{{ __('Your registration was denied. Please contact an administrator.') }}
                </h2>
            </div>
        </div>
    </div>
</div>
@endsection
