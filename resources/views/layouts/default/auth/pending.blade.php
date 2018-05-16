@extends('app')
@section('title', __('Registration Pending'))

@section('content')
<div class="row">
    <div class="col-md-12 " style="text-align: center;">
        <div class="flex-center position-ref full-height">
            <div class="title m-b-md">
                <h2 class="description">{{ __('Your registration is pending approval. Please check your email!') }}</h2>
            </div>
        </div>
    </div>
</div>
@endsection()
