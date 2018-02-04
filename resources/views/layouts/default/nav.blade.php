<ul class="navbar-nav">
    @if(Auth::check())
        <li class="nav-item">
            <a class="nav-link" href="{!! url('/dashboard') !!}">
                <i class="fa fa-tachometer"></i>
                <p>Dashboard</p>
            </a>
        </li>
    @endif

    <li class="nav-item">
        <a class="nav-link" href="{!! url('/livemap') !!}">
            <i class="fa fa-globe"></i>
            <p>Live Map</p>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="{!! url('/pilots') !!}">
            <i class="fa fa-users"></i>
            <p>Pilots</p>
        </a>
    </li>

    {{-- Show the module links that don't require being logged in --}}
    @foreach($moduleSvc->getFrontendLinks($logged_in=false) as &$link)
        <li class="nav-item">
            <a class="nav-link" href="{!! url($link['url']) !!}">
                <i class="{!! $link['icon'] !!}"></i>
                <p>{!! $link['title'] !!}</p>
            </a>
        </li>
    @endforeach

    @if(!Auth::check())
        <li class="nav-item">
            <a class="nav-link" href="{!! url('/login') !!}">
                <i class="fa fa-sign-in"></i>
                <p>Login</p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{!! url('/register') !!}">
                <i class="fa fa-id-card-o"></i>
                <p>Register</p>
            </a>
        </li>

    @else

        <li class="nav-item">
            <a class="nav-link" href="{!! url('/flights') !!}">
                <i class="fa fa-plane"></i>
                <p>Flights</p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{!! url('/pireps') !!}">
                <i class="fa fa-cloud-upload"></i>
                <p>PIREPs</p>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{!! url('/profile') !!}">
                <i class="fa fa-user-circle-o"></i>
                <p>Profile</p>
            </a>
        </li>

        @role('admin')
        <li class="nav-item">
            <a class="nav-link" href="{!! url('/admin') !!}">
                <i class="fa fa-circle-o-notch"></i>
                <p>Admin</p>
            </a>
        </li>
        @endrole

        {{-- Show the module links for being logged in --}}
        @foreach($moduleSvc->getFrontendLinks($logged_in=true) as &$link)
            <li class="nav-item">
                <a class="nav-link" href="{!! url($link['url']) !!}">
                    <i class="{!! $link['icon'] !!}"></i>
                    <p>{!! $link['title'] !!}</p>
                </a>
            </li>
        @endforeach

        <li class="nav-item">
            <a class="nav-link" href="{!! url('/logout') !!}">
                <i class="fa fa-external-link-square"></i>
                <p>Log Out</p>
            </a>
        </li>
    @endif
</ul>
