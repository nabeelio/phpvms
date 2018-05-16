<ul class="navbar-nav">
    @if(Auth::check())
        <li class="nav-item">
            <a class="nav-link" href="{{ route('frontend.dashboard.index') }}">
                <i class="fas fa-tachometer-alt"></i>
                <p>@lang('frontend.global.dashboard')</p>
            </a>
        </li>
    @endif

    <li class="nav-item">
        <a class="nav-link" href="{{ route('frontend.livemap.index') }}">
            <i class="fas fa-globe"></i>
            <p>@lang('frontend.global.livemap')</p>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('frontend.pilots.index') }}">
            <i class="fas fa-users"></i>
            <p>{{ trans_choice('frontend.global.pilot', 2) }}</p>
        </a>
    </li>

    {{-- Show the module links that don't require being logged in --}}
    @foreach($moduleSvc->getFrontendLinks($logged_in=false) as &$link)
        <li class="nav-item">
            <a class="nav-link" href="{{ url($link['url']) }}">
                <i class="{{ $link['icon'] }}"></i>
                <p>{{ ($link['title']) }}</p>
            </a>
        </li>
    @endforeach

    @if(!Auth::check())
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/login') }}">
                <i class="fas fa-sign-in-alt"></i>
                <p>@lang('frontend.global.login')</p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/register') }}">
                <i class="far fa-id-card"></i>
                <p>@lang('frontend.global.register')</p>
            </a>
        </li>

    @else

        <li class="nav-item">
            <a class="nav-link" href="{{ route('frontend.flights.index') }}">
                <i class="fab fa-avianex"></i>
                <p>{{ trans_choice('frontend.global.flight', 2) }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('frontend.pireps.index') }}">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>{{ trans_choice('frontend.global.pirep', 2) }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('frontend.profile.index') }}">
                <i class="far fa-user"></i>
                <p>@lang('frontend.global.profile')</p>
            </a>
        </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('frontend.downloads.index') }}">
                    <i class="fas fa-download"></i>
                    <p>{{ trans_choice('frontend.global.download', 2) }}</p>
                </a>
            </li>

        @role('admin')
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/admin') }}">
                <i class="fas fa-circle-notch"></i>
                <p>@lang('frontend.nav.administration')</p>
            </a>
        </li>
        @endrole

        {{-- Show the module links for being logged in --}}
        @foreach($moduleSvc->getFrontendLinks($logged_in=true) as &$link)
            <li class="nav-item">
                <a class="nav-link" href="{{ url($link['url']) }}">
                    <i class="{{ $link['icon'] }}"></i>
                    <p>{{ ($link['title']) }}</p>
                </a>
            </li>
        @endforeach

        <li class="nav-item">
            <a class="nav-link" href="{{ url('/logout') }}">
                <i class="fas fa-sign-out-alt"></i>
                <p>@lang('frontend.nav.logout')</p>
            </a>
        </li>
    @endif
</ul>
