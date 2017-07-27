
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta name="keyword" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>phpvms login</title>

    <link rel="stylesheet" type="text/css" href="assets/frontend/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/frontend/css/plugins/font-awesome.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/frontend/css/plugins/simple-line-icons.css"/>
    <link rel="stylesheet" type="text/css" href="assets/frontend/css/plugins/animate.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/frontend/css/plugins/icheck/skins/flat/aero.css"/>
    <link href="assets/frontend/css/style.css" rel="stylesheet">

    <link rel="shortcut icon" href="assets/frontend/img/logomi.png">
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body id="mimin" class="dashboard form-signin-wrapper">

<div class="container">

    @yield('content')

</div>

<!-- end: Content -->
<!-- start: Javascript -->
<script src="assets/frontend/js/jquery.min.js"></script>
<script src="assets/frontend/js/jquery.ui.min.js"></script>
<script src="assets/frontend/js/bootstrap.min.js"></script>

<script src="assets/frontend/js/plugins/moment.min.js"></script>
<script src="assets/frontend/js/plugins/icheck.min.js"></script>

<!-- custom -->
<script src="assets/frontend/js/main.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('input').iCheck({
            checkboxClass: 'icheckbox_flat-aero',
            radioClass: 'iradio_flat-aero'
        });
    });
</script>
<!-- end: Javascript -->
</body>
</html>
