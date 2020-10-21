<li>
  <a href="{{ url('/admin/dashboard') }}"><i class="pe-7s-display1"></i>Dashboard</a>
</li>

<li>
  <a data-toggle="collapse" href="#operations_menu" class="menu operations_menu" aria-expanded="true">
    <h5>Operations&nbsp;<b class="pe-7s-angle-right"></b></h5>
  </a>

  <div class="collapse" id="operations_menu" aria-expanded="true">
    <ul class="nav">
      @ability('admin', 'pireps')
      <li><a href="{{ url('/admin/pireps') }}"><i class="pe-7s-cloud-upload"></i>PIREPs
          <span data-toggle="tooltip" title="3 New" class="badge bg-light-blue pull-right">3</span>
        </a>
      </li>
      @endability

      @ability('admin', 'flights')
      <li><a href="{{ url('/admin/flights') }}"><i class="pe-7s-vector"></i>Flights</a></li>
      @endability

      @ability('admin', 'fleet')
      <li><a href="{{ url('/admin/subfleets') }}"><i class="pe-7s-plane"></i>Fleet</a></li>
      @endability

      @ability('admin', 'fares')
      <li><a href="{{ url('/admin/fares') }}"><i class="pe-7s-graph2"></i>Fares</a></li>
      @endability

      @ability('admin', 'finances')
      <li><a href="{{ url('/admin/finances') }}"><i class="pe-7s-display1"></i>Finances</a></li>
      @endability

      @ability('admin', 'users')
      <li><a href="{{ url('/admin/users') }}"><i class="pe-7s-users"></i>Users</a></li>
      @endability
    </ul>
  </div>
</li>

<li>
  <a data-toggle="collapse" href="#config_menu" class="menu config_menu" aria-expanded="true">
    <h5>Config&nbsp;<b class="pe-7s-angle-right"></b></h5>
  </a>

  <div class="collapse" id="config_menu" aria-expanded="true">
    <ul class="nav">
      @ability('admin', 'airlines')
      <li><a href="{{ url('/admin/airlines') }}"><i class="pe-7s-paper-plane"></i>Airlines</a></li>
      @endability

      @ability('admin', 'airports')
      <li><a href="{{ url('/admin/airports') }}"><i class="pe-7s-map-marker"></i>Airports</a></li>
      @endability

      @ability('admin', 'finances')
      <li><a href="{{ url('/admin/expenses') }}"><i class="pe-7s-cash"></i>Expenses</a></li>
      @endability

      @ability('admin', 'ranks')
      <li><a href="{{ url('/admin/ranks') }}"><i class="pe-7s-graph1"></i>Ranks</a></li>
      @endability

      @ability('admin', 'awards')
      <li><a href="{!! url('/admin/awards') !!}"><i class="pe-7s-diamond"></i>Awards</a></li>
      @endability

      @ability('admin', 'users')
      <li><a href="{!! url('/admin/roles') !!}"><i class="pe-7s-network"></i>Roles</a></li>
      @endability

      @ability('admin', 'pages')
      <li><a href="{!! url('/admin/pages') !!}"><i class="pe-7s-note"></i>Pages/Links</a></li>
      @endability

      @ability('admin', 'modules')
      <li><a href="{!! url('/admin/modules') !!}"><i class="pe-7s-box2"></i>Modules Manager</a></li>
      @endability

      @ability('admin', 'maintenance')
      <li><a href="{{ url('/admin/maintenance') }}"><i class="pe-7s-tools"></i>Maintenance</a></li>
      @endability

      @ability('admin', 'settings')
      <li><a href="{{ url('/admin/settings') }}"><i class="pe-7s-config"></i>Settings</a></li>
      @endability
    </ul>
  </div>
</li>

<li>
  <a data-toggle="collapse" href="#addons_menu" class="menu addons_menu" aria-expanded="true">
    <h5>Addons&nbsp;<b class="pe-7s-angle-right"></b></h5>
  </a>

  <div class="collapse" id="addons_menu" aria-expanded="true">
    <ul class="nav">
      @ability('admin', 'addons')
      @foreach($moduleSvc->getAdminLinks() as &$link)
        <li><a href="{{ url($link['url']) }}"><i class="{{ $link['icon'] }}"></i>{{ $link['title'] }}</a></li>
      @endforeach
      @endability
    </ul>
  </div>
</li>

