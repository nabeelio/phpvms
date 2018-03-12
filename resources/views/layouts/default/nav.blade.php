<ul class="navbar-nav">
    @if(Auth::check())
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/dashboard') }}">
                <i class="fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>
    @endif

    <li class="nav-item">
        <a class="nav-link" href="{{ url('/livemap') }}">
            <i class="fas fa-globe"></i>
            <p>Live Map</p>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{{ url('/pilots') }}">
            <i class="fas fa-users"></i>
            <p>Pilots</p>
        </a>
    </li>

    {{-- Show the module links that don't require being logged in --}}
    @foreach($moduleSvc->getFrontendLinks($logged_in=false) as &$link)
        <li class="nav-item">
            <a class="nav-link" href="{{ url($link['url']) }}">
                <i class="{{ $link['icon'] }}"></i>
                <p>{{ $link['title'] }}</p>
            </a>
        </li>
    @endforeach

    @if(!Auth::check())
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/login') }}">
                <i class="fas fa-sign-in-alt"></i>
                <p>Login</p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/register') }}">
                <i class="far fa-id-card"></i>
                <p>Register</p>
            </a>
        </li>

    @else

        <li class="nav-item">
            <a class="nav-link" href="{{ url('/flights') }}">
                <i class="fab fa-avianex"></i>
                <p>Flights</p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/pireps') }}">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Flight Reports</p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/profile') }}">
                <i class="far fa-user"></i>
                <p>Profile</p>
            </a>
        </li>

        @role('admin')
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/admin') }}">
                <i class="fas fa-circle-notch"></i>
                <p>Admin</p>
            </a>
        </li>
        @endrole

        {{-- Show the module links for being logged in --}}
        @foreach($moduleSvc->getFrontendLinks($logged_in=true) as &$link)
            <li class="nav-item">
                <a class="nav-link" href="{{ url($link['url']) }}">
                    <i class="{{ $link['icon'] }}"></i>
                    <p>{{ $link['title'] }}</p>
                </a>
            </li>
        @endforeach

        <li class="nav-item">
            <a class="nav-link" href="{{ url('/logout') }}">
                <i class="fas fa-sign-out-alt"></i>
                <p>Log Out</p>
            </a>
        </li>
    @endif
</ul>
