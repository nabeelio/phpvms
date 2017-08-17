<li class="active">
    <a href="{!! url('/admin/dashboard') !!}"><i class="fa fa-dashboard"></i>dashboard</a>
</li>

<li><h3>operations</h3></li>
<li><a href="{!! url('/admin/pireps') !!}"><i class="fa fa-inbox"></i>&nbsp;pireps
        <span data-toggle="tooltip" title="3 New" class="badge bg-light-blue pull-right">3</span>
    </a>
</li>
<li><a href="{!! url('/admin/flights') !!}"><i class="fa fa-map"></i>&nbsp;flights</a></li>
<li><a href="{!! url('/admin/aircraft') !!}"><i class="fa fa-plane" aria-hidden="true"></i>&nbsp;fleet</a></li>
<li><a href="{!! url('/admin/fares') !!}"><i class="fa fa-dollar"></i>&nbsp;fares</a></li>

<li class="header">config</li>
<li><a href="{!! url('/admin/airlines') !!}"><i class="fa fa-navicon"></i>&nbsp;airlines</a></li>
<li><a href="{!! url('/admin/airports') !!}"><i class="fa fa-globe"></i>&nbsp;airports</a></li>
{{--<li><a href="{!! url('/admin/aircraftclasses') !!}"><i class="fa fa-tag"></i>&nbsp;aircraft classes</a></li>--}}
<li><a href="{!! url('/admin/users') !!}"><i class="fa fa-user-circle-o" aria-hidden="true"></i>&nbsp;users</a></li>
<li><a href="{!! url('/admin/ranks') !!}"><i class="fa fa-id-card-o" aria-hidden="true"></i>&nbsp;ranks</a></li>
