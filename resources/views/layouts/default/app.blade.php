<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport'/>

    <title>@yield('title') - {{ config('app.name') }}</title>
    {{-- Start of required lines block. DON'T REMOVE THESE LINES! They're required or might break things --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-key" content="{{ Auth::check() ? Auth::user()->api_key: '' }}">
    {{-- End the required lines block --}}

    <link rel="shortcut icon" type="image/png" href="{{ public_asset('/assets/img/favicon.png') }}"/>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet"/>
    <link href="{{ public_asset('/assets/frontend/css/bootstrap.min.css') }}" rel="stylesheet"/>
    <link href="{{ public_asset('/assets/frontend/css/now-ui-kit.css') }}" rel="stylesheet"/>

    {{-- Start of the required files in the head block --}}
    <link href="{{ public_asset('/assets/global/css/vendor.css') }}" rel="stylesheet"/>
    @yield('css')
    @yield('scripts_head')
    {{-- End of the required stuff in the head block --}}

    <link href="{{ public_asset('/assets/frontend/css/styles.css') }}" rel="stylesheet"/>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-toggleable-md" style="background: #067ec1;">
    <div class="container" style="width: 85%!important;">
        <div class="navbar-translate">
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse"
                    data-target="#navigation" aria-controls="navigation-index" aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
            </button>
            <p class="navbar-brand text-white" data-placement="bottom" target="_blank">
                <a href="{{ url('/') }}">
                    <img src="{{ public_asset('/assets/img/logo_blue_bg.svg') }}" width="135px" style=""/>
                </a>
            </p>
        </div>
        <div class="collapse navbar-collapse justify-content-end" id="navigation">
            @include('nav')
        </div>
    </div>
</nav>
<!-- End Navbar -->
<div class="clearfix" style="height: 25px;"></div>
<div class="wrapper">
    <div class="clear"></div>
    <div class="container-fluid" style="width: 85%!important;">

        {{-- These should go where you want your content to show up --}}
        @include('flash.message')
        @yield('content')
        {{-- End the above block--}}

    </div>
    <div class="clearfix" style="height: 200px;"></div>

    <footer class="footer footer-default">
        <div class="container">
            <div class="copyright">
                {{--
                Please keep the copyright message somewhere, as-per the LICENSE file
                        Thanks!!
                --}}
                powered by <a href="http://www.phpvms.net" target="_blank">phpvms</a>
            </div>
        </div>
    </footer>
</div>

<script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>

{{-- Start of the required tags block. Don't remove these or things will break!! --}}
<script src="{{ public_asset('/assets/global/js/vendor.js') }}"></script>
<script src="{{ public_asset('/assets/frontend/js/app.js') }}"></script>
{{--<script src="bower_components/sightglass/index.js"></script>
<script src="bower_components/rivets/dist/rivets.min.js"></script>--}}
@yield('scripts')
{{-- End the required tags block --}}

<script>
$(document).ready(function () {
    $(".select2").select2({width: 'resolve'});
});
</script>

</body>
</html>
