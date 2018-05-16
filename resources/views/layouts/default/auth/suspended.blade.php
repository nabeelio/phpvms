@extends('app')
@section('title', __('Account Suspended'))

@section('content')
<div class="row">
    <div class="col-md-12 " style="text-align: center;">
        <div class="flex-center position-ref full-height">
            <div class="title m-b-md">
                <h2 class="description">
				{{ __('Your account has been suspended. Please contact an administrator.') }}
                </h2>
            </div>
        </div>
    </div>
</div>
@endsection
