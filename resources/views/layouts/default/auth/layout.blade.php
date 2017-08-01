
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="/assets/frontend/img/apple-icon.png">
    <link rel="icon" type="image/png" href="/assets/frontend/img/favicon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Login Page - Now Ui Kit by Creative Tim</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />
    <!-- CSS Files -->
    <link href="/assets/frontend/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/assets/frontend/css/now-ui-kit.css" rel="stylesheet" />
    <!-- CSS Just for demo purpose, don't include it in your project -->

    @yield('css')
</head>

<body class="login-page" style="background: #067ec1;">
<!-- Navbar -->
<nav class="navbar navbar-toggleable-md bg-primary fixed-top navbar-transparent " color-on-scroll="500">
    <div class="container">

        <div class="navbar-translate">
            <a class="navbar-brand" href="http://demos.creative-tim.com/now-ui-kit/index.html" rel="tooltip" title="Designed by Invision. Coded by Creative Tim" data-placement="bottom" target="_blank">
                phpVMS
            </a>
        </div>

    </div>
</nav>
<!-- End Navbar -->
<div class="page-header">

    <div class="container">
        @yield('content')
    </div>
    <footer class="footer">
        <div class="container">
            <div class="copyright">
                &copy;
                <script>
                    document.write(new Date().getFullYear())
                </script>, powered by
                <a href="http://www.phpvms.net" target="_blank">phpvms</a>. Now-UI by
                <a href="https://www.creative-tim.com" target="_blank">Creative Tim</a>
            </div>
        </div>
    </footer>
</div>
</body>
<!--   Core JS Files   -->
<script src="/assets/frontend/js/core/jquery.3.2.1.min.js" type="text/javascript"></script>
<script src="/assets/frontend/js/core/tether.min.js" type="text/javascript"></script>
<script src="/assets/frontend/js/core/bootstrap.min.js" type="text/javascript"></script>
<!--  Plugin for Switches, full documentation here: http://www.jque.re/plugins/version3/bootstrap.switch/ -->
<script src="./assets/frontend/js/plugins/bootstrap-switch.js"></script>
<!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
<script src="/assets/frontend/js/plugins/nouislider.min.js" type="text/javascript"></script>
<!--  Plugin for the DatePicker, full documentation here: https://github.com/uxsolutions/bootstrap-datepicker -->
<script src="/assets/frontend/js/plugins/bootstrap-datepicker.js" type="text/javascript"></script>
<!-- Control Center for Now Ui Kit: parallax effects, scripts for the example pages etc -->
<script src="/assets/frontend/js/now-ui-kit.js" type="text/javascript"></script>

@yield('scripts')

</html>
