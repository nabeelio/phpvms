@extends('app')
@section('title', __('Welcome!'))

@section('content')
    <div class="row">
        <div class="col-sm-12">
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <center><h1 class="description">{{ __('Welcome Message') }}</h1></center>
        </div>
		<div class="col-sm-9">
			<img src="{{ public_asset('/assets/img/Airplane.jpg') }}" style=""/>
		</div>
        <div class="col-sm-3 ">
            <h2 class="description">{{ __('Newest Pilots') }}</h2>
			@foreach($users as $user)
            <div class="card card-signup blue-bg">
                {{--<div class="card-bg">--}}
                    {{--<i class="fa fa-user-o" style="opacity: .1;"></i>--}}
                {{--</div>--}}
                <div class="header header-primary text-center blue-bg">
                    <h3 class="title title-up text-white">
                        <a href="{{ route('frontend.profile.show', ['id' => $user->id]) }}" class="text-white">{{ $user->name }}</a>
                    </h3>
                    <div class="photo-container">
					@if ($user->avatar == null)
                        <img class="rounded-circle"
                             src="{{ $user->gravatar(123) }}">
					@else
						<img src="{{ $user->avatar->url }}" style="width: 123px;">
					@endif
                    </div>
                </div>
                <div class="content content-center">
                    <div class="social-description text-center text-white">
                        <h2 class="description text-white">
                            @if(filled($user->home_airport))
                            {{ $user->home_airport->icao }}
                            @endif
                        </h2>
                    </div>
                </div>
                <div class="footer text-center">
                    <a href="{{ route('frontend.profile.show', ['id' => $user->id]) }}" class="btn btn-neutral btn-sm">{{ __('Profile') }}</a>
                </div>
            </div>
			@endforeach
        </div>
    </div>
@endsection
