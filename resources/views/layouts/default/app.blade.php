
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta name="description" content="Miminium Admin Template v.1">
    <meta name="author" content="Isna Nur Azis">
    <meta name="keyword" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Miminium</title>

    <!-- start: Css -->
    <link rel="stylesheet" type="text/css" href="/assets/frontend/css/bootstrap.min.css">

    <!-- plugins -->
    <link rel="stylesheet" type="text/css" href="/assets/frontend/css/plugins/font-awesome.min.css"/>
    <link rel="stylesheet" type="text/css" href="/assets/frontend/css/plugins/simple-line-icons.css"/>
    <link rel="stylesheet" type="text/css" href="/assets/frontend/css/plugins/mediaelementplayer.css"/>
    <link rel="stylesheet" type="text/css" href="/assets/frontend/css/plugins/animate.min.css"/>
    <link href="/assets/frontend/css/style.css" rel="stylesheet">

@yield('css')
<!-- end: Css -->

    <link rel="shortcut icon" href="/assets/frontend/img/logomi.png">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body id="mimin" class="dashboard topnav">
<!-- start: Header -->
<nav class="navbar navbar-default header navbar-fixed-top">
    <div class="col-md-12 nav-wrapper">
        <div class="navbar-header" style="width:100%;">
            <a href="{{ url('/') }}" class="navbar-brand">
                <b>phpvms</b>
            </a>

            <ul class="nav navbar-nav search-nav">
                <li class="active"><a href="#">Menu Item</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Action</a></li>
                        <li><a href="#">Another action</a></li>
                        <li><a href="#">Something else here</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#">Separated link</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#">One more separated link</a></li>
                    </ul>
                </li>
            </ul>

            <ul class="nav navbar-nav navbar-right user-nav">
                <li class="user-name"><span>Akihiko Avaron</span></li>
                <li class="dropdown avatar-dropdown">
                    <img src="asset/img/avatar.jpg" class="img-circle avatar" alt="user name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"/>
                    <ul class="dropdown-menu user-dropdown">
                        <li><a href="#"><span class="fa fa-user"></span> My Profile</a></li>
                        <li><a href="#"><span class="fa fa-calendar"></span> My Calendar</a></li>
                        <li role="separator" class="divider"></li>
                        <li class="more">
                            <ul>
                                <li><a href=""><span class="fa fa-cogs"></span></a></li>
                                <li><a href=""><span class="fa fa-lock"></span></a></li>
                                <li><a href=""><span class="fa fa-power-off "></span></a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- end: Header -->

<!-- start: Content -->
<div id="content">
    @yield('content')
</div>
<!-- end: content -->

<!-- start: Javascript -->
<script src="/assets/frontend/js/jquery.min.js"></script>
<script src="/assets/frontend/js/jquery.ui.min.js"></script>
<script src="/assets/frontend/js/bootstrap.min.js"></script>



<!-- plugins -->
<script src="/assets/frontend/js/plugins/holder.min.js"></script>
<script src="/assets/frontend/js/plugins/moment.min.js"></script>
<script src="/assets/frontend/js/plugins/jquery.nicescroll.js"></script>


<!-- custom -->
<script src="/assets/frontend/js/main.js"></script>
<script type="text/javascript">
    $(document).ready(function(){

    });
</script>

@yield('scripts')

<!-- end: Javascript -->
</body>
</html>
