<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport'/>

    <title>@yield('title') - {{ config('app.name') }}</title>

    {{-- Start of required lines block. DON'T REMOVE THESE LINES! They're required or might break things --}}
    <meta name="base-url" content="{!! url('') !!}">
    <meta name="api-key" content="{!! Auth::check() ? Auth::user()->api_key: '' !!}">
    <meta name="csrf-token" content="{!! csrf_token() !!}">
    {{-- End the required lines block --}}

    <link rel="shortcut icon" type="image/png" href="{{ public_asset('/assets/img/favicon.png') }}"/>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet"/>
    <link href="{{ public_asset('/assets/frontend/css/bootstrap.min.css') }}" rel="stylesheet"/>
    <link href="{{ public_asset('/assets/frontend/css/now-ui-kit.css') }}" rel="stylesheet"/>
    <link href="{{ public_asset('/assets/frontend/css/styles.css') }}" rel="stylesheet"/>

    {{-- Start of the required files in the head block --}}
    <link href="{{ public_mix('/assets/global/css/vendor.css') }}" rel="stylesheet"/>
    <style type="text/css">
    @yield('css')
    </style>

    <script>
    @yield('scripts_head')
    </script>
    {{-- End of the required stuff in the head block --}}

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
<div id="top_anchor" class="clearfix" style="height: 25px;"></div>
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
<script src="{{ public_mix('/assets/global/js/vendor.js') }}"></script>
<script src="{{ public_mix('/assets/frontend/js/app.js') }}"></script>
@yield('scripts')

{{--
It's probably safe to keep this to ensure you're in compliance
with the EU Cookie Law https://privacypolicies.com/blog/eu-cookie-law
--}}
<script>
  window.addEventListener("load", function () {
    window.cookieconsent.initialise({
      palette: {
        popup: {
          background: "#edeff5",
          text: "#838391"
        },
        button: {
          "background": "#067ec1"
        }
      },
      position: "top",
    })
  });
</script>
{{-- End the required tags block --}}

<script>
  $(document).ready(function () {
    $(".select2").select2({width: 'resolve'});
  });
</script>
</body>
</html>
