<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - importer</title>

    <link rel="shortcut icon" type="image/png" href="{{ public_asset('/assets/img/favicon.png') }}"/>

    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no'
          name='viewport'/>
    <meta name="base-url" content="{!! url('') !!}">
    <meta name="api-key" content="{!! Auth::check() ? Auth::user()->api_key: '' !!}">
    <meta name="csrf-token" content="{!! csrf_token() !!}">

    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet"/>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet"/>

    <link href="{{ public_asset('/assets/frontend/css/bootstrap.min.css') }}" rel="stylesheet"/>
    <link href="{{ public_asset('/assets/frontend/css/now-ui-kit.css') }}" rel="stylesheet"/>
    <link href="{{ public_asset('/assets/installer/css/vendor.css') }}" rel="stylesheet"/>
    <link href="{{ public_asset('/assets/frontend/css/styles.css') }}" rel="stylesheet"/>

    <link rel="stylesheet"
          href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/styles/default.min.css">

    <style>
        .table tr:first-child td { border-top: 0px; }
        @yield('css')
    </style>
</head>

<body>
<!-- Navbar -->
<nav class="navbar navbar-toggleable-md" style="background: #067ec1;">
    <div class="container" style="width: 85%!important;">
        <div class="navbar-translate">
            <p class="navbar-brand text-white" data-placement="bottom" target="_blank">
                <a href="{{ url('/') }}">
                    <img src="{{ public_asset('/assets/img/logo_blue_bg.svg') }}" width="135px" style=""/>
                </a>
            </p>
        </div>
        <div class="justify-content-center" id="navigation" style="margin-left: 50px; color: white; font-size: 20px;">
            @yield('title')
        </div>
    </div>
</nav>
<!-- End Navbar -->
{{--<div class="clearfix" style="height: 25px;"></div>--}}
<div class="wrapper">
    <div class="clear"></div>
    <div class="container" style="width: 50%">
        <div class="row">
            <div class="col-12">
                @include('importer::flash.message')
                @yield('content')
            </div>
        </div>
    </div>
    <div class="clearfix" style="height: 200px;"></div>
</div>

{{--<script src="https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js"></script>--}}

<script src="{{ public_mix('/assets/global/js/vendor.js') }}"></script>
<script src="{{ public_mix('/assets/frontend/js/vendor.js') }}"></script>
<script src="{{ public_mix('/assets/frontend/js/app.js') }}"></script>
<script src="{{ public_asset('/assets/installer/js/vendor.js') }}" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js"></script>

<script>
    hljs.configure({languages: ['sh']});

    $(document).ready(function () {

        $(".select2").select2();

        $('pre code').each(function (i, block) {
            hljs.fixMarkup(block);
            hljs.highlightBlock(block);
        });
    });
</script>

@yield('scripts')

</body>
</html>
