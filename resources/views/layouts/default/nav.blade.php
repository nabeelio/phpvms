<ul class="navbar-nav">
    @if(Auth::check())
        <li class="nav-item">
            <a class="nav-link" href="{{ route('frontend.dashboard.index') }}">
                <i class="fas fa-tachometer-alt"></i>
                <p>{{ __('Dashboard') }}</p>
            </a>
        </li>
    @endif

    <li class="nav-item">
        <a class="nav-link" href="{{ route('frontend.livemap.index') }}">
            <i class="fas fa-globe"></i>
            <p>{{ __('Live Map') }}</p>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ route('frontend.pilots.index') }}">
            <i class="fas fa-users"></i>
            <p>{{ __trans_choice('Pilot', 2) }}</p>
        </a>
    </li>

    {{-- Show the module links that don't require being logged in --}}
    @foreach($moduleSvc->getFrontendLinks($logged_in=false) as &$link)
        <li class="nav-item">
            <a class="nav-link" href="{{ url($link['url']) }}">
                <i class="{{ $link['icon'] }}"></i>
                <p>{{ __($link['title']) }}</p>
            </a>
        </li>
    @endforeach

    @if(!Auth::check())
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/login') }}">
                <i class="fas fa-sign-in-alt"></i>
                <p>{{ __('Login') }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/register') }}">
                <i class="far fa-id-card"></i>
                <p>{{ __('Register') }}</p>
            </a>
        </li>

    @else

        <li class="nav-item">
            <a class="nav-link" href="{{ route('frontend.flights.index') }}">
                <i class="fab fa-avianex"></i>
                <p>{{ __trans_choice('Flight', 2) }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('frontend.pireps.index') }}">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>{{ __trans_choice('PIREP', 2) }}</p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('frontend.profile.index') }}">
                <i class="far fa-user"></i>
                <p>{{ __('Profile') }}</p>
            </a>
        </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('frontend.downloads.index') }}">
                    <i class="fas fa-download"></i>
                    <p>{{ __trans_choice('Download', 2) }}</p>
                </a>
            </li>

        @role('admin')
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/admin') }}">
                <i class="fas fa-circle-notch"></i>
                <p>{{ __('Administration') }}</p>
            </a>
        </li>
        @endrole

        {{-- Show the module links for being logged in --}}
        @foreach($moduleSvc->getFrontendLinks($logged_in=true) as &$link)
            <li class="nav-item">
                <a class="nav-link" href="{{ url($link['url']) }}">
                    <i class="{{ $link['icon'] }}"></i>
                    <p>{{ __($link['title']) }}</p>
                </a>
            </li>
        @endforeach

        <li class="nav-item">
            <a class="nav-link" href="{{ url('/logout') }}">
                <i class="fas fa-sign-out-alt"></i>
                <p>{{ __('Log Out') }}</p>
            </a>
        </li>
    @endif
</ul>
