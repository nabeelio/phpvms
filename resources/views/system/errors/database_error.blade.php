<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8"/>
  <link rel="apple-touch-icon" sizes="76x76" href="/assets/frontend/img/apple-icon.png">
  <link rel="icon" type="image/png" href="/assets/frontend/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
  <title>@yield('title') - installer</title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no'
        name='viewport'/>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet"/>
  <link rel="stylesheet"
        href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css"/>
  <!-- CSS Files -->
  <link href="{{ public_asset('/assets/frontend/css/bootstrap.min.css') }}" rel="stylesheet"/>
  <link href="{{ public_asset('/assets/frontend/css/now-ui-kit.css') }}" rel="stylesheet"/>
  <link href="{{ public_asset('/assets/frontend/css/styles.css') }}" rel="stylesheet"/>
  {{--<link href="/assets/frontend/css/installer.css" rel="stylesheet"/>--}}

  <link rel="stylesheet"
        href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/styles/default.min.css">

  <style>
    .table tr:first-child td {
      border-top: 0px;
    }
  </style>
  @yield('css')
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
          <img src="{{ public_asset('/assets/frontend/img/logo_blue_bg.svg') }}" width="135px" style=""/>
        </a>
      </p>
    </div>
    <div class="justify-content-center" id="navigation"
         style="margin-left: 50px; color: white; font-size: 20px;">
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
        <h2>Database Error</h2>
        {{ $error }}
      </div>
    </div>
  </div>
  <div class="clearfix" style="height: 200px;"></div>
</div>

@yield('scripts')

</body>
</html>
