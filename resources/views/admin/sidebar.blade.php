<aside class="main-sidebar" id="sidebar-wrapper">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        {{--<div class="user-panel">
            <div class="pull-left image">
                <img src="http://infyom.com/images/logo/blue_logo_150x150.jpg" class="img-circle"
                     alt="User Image"/>
            </div>
            <div class="pull-left info">
                @if (Auth::guest())
                <p>phpVMS Admin</p>
                @else
                    <p>{{ Auth::user()->name}}</p>
                @endif
                <!-- Status -->
                --}}{{--<a href="#"><i class="fa fa-circle text-success"></i> Online</a>--}}{{--
            </div>
        </div>--}}
        <br />

        <ul class="sidebar-menu">
            @include('admin.menu')
        </ul>
        <!-- /.sidebar-menu -->
        <div class="panel-footer" style="position:absolute;bottom: 0; width:100%; text-align: center;">
            <p class="small" style="padding-top: 5px;">
                copyright &copy; 2017
                <a href="http://www.phpvms.net" target="_blank">phpvms</a>
            </p>
        </div>
    </section>
    <!-- /.sidebar -->
</aside>
