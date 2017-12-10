
<li>
    <a href="{!! url('/admin/dashboard') !!}"><i class="pe-7s-display1"></i>dashboard</a>
</li>

<li>
    <a data-toggle="collapse" href="#operations_menu" class="menu operations_menu" aria-expanded="true">
        <h5>operations&nbsp;<b class="pe-7s-angle-up"></b></h5>
    </a>

    <div class="collapse" id="operations_menu" aria-expanded="true">
        <ul class="nav">
            <li><a href="{!! url('/admin/pireps') !!}"><i class="pe-7s-cloud-upload"></i>pireps
                    <span data-toggle="tooltip" title="3 New"
                          class="badge bg-light-blue pull-right">3</span>
                </a>
            </li>
            <li><a href="{!! url('/admin/flights') !!}"><i class="pe-7s-vector"></i>flights</a></li>
            <li><a href="{!! url('/admin/aircraft') !!}"><i class="pe-7s-plane"></i>fleet</a></li>
            <li><a href="{!! url('/admin/fares') !!}"><i class="pe-7s-graph2"></i>fares</a></li>
        </ul>
    </div>
</li>

<li>
    <a data-toggle="collapse" href="#config_menu" class="menu config_menu" aria-expanded="true">
        <h5>config&nbsp;<b class="pe-7s-angle-up"></b></h5>
    </a>

    <div class="collapse" id="config_menu" aria-expanded="true">
        <ul class="nav">
            <li><a href="{!! url('/admin/airlines') !!}"><i
                            class="pe-7s-paper-plane"></i>airlines</a></li>
            <li><a href="{!! url('/admin/airports') !!}"><i
                            class="pe-7s-map-marker"></i>airports</a></li>
            <li><a href="{!! url('/admin/users') !!}"><i class="pe-7s-users"></i>users</a></li>
            <li><a href="{!! url('/admin/ranks') !!}"><i class="pe-7s-id"></i>ranks</a></li>
            <li><a href="{!! url('/admin/settings') !!}"><i class="pe-7s-id"></i>settings</a></li>
        </ul>
    </div>
</li>

<li>
    <a data-toggle="collapse" href="#addons_menu" class="menu addons_menu" aria-expanded="true">
        <h5>addons&nbsp;<b class="pe-7s-angle-up"></b></h5>
    </a>

    <div class="collapse" id="addons_menu" aria-expanded="true">
        <ul class="nav">
            @inject('moduleSvc', 'App\Services\ModuleService')
            @foreach($moduleSvc->getAdminLinks() as &$link)
                <li><a href="{!! url($link['url']) !!}"><i class="{!! $link['icon'] !!}"></i>{!! $link['title'] !!}</a></li>
            @endforeach
        </ul>
    </div>
</li>

