<!doctype html>
<html lang="en">
<head>
    <title>@yield('title') - {!! config('app.name') !!} admin</title>

    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />

    <link rel="icon" type="image/png" href="{!! public_asset('/assets/admin/img/favicon.png') !!}">

    <link href='https://fonts.googleapis.com/css?family=Muli:400,300' rel='stylesheet' type='text/css'>
    <link href="http://fonts.googleapis.com/css?family=Roboto:400,700,300" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="{{ public_asset('/assets/admin/css/vendor.min.css') }}">
    <link rel="stylesheet" href="{{ public_asset('/assets/admin/css/admin.css') }}">

    <style type="text/css">
    @yield('css')
    </style>

    <script>
    @if (Auth::user())
        const PHPVMS_USER_API_KEY = "{!! Auth::user()->api_key !!}";
    @else
        const PHPVMS_USER_API_KEY = false;
    @endif
    </script>

</head>
<body>

<div class="wrapper">
    @include('admin.sidebar')

    <div class="main-panel">
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar bar1"></span>
                        <span class="icon-bar bar2"></span>
                        <span class="icon-bar bar3"></span>
                    </button>
                    <a class="navbar-brand" href="#">@yield('title')</a>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        @yield('actions')
                    </ul>

                </div>
            </div>
        </nav>


        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        @include('flash::message')
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


</body>

{{--<script src="https://cdn.jsdelivr.net/lodash/4.17.4/lodash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="{!! public_asset('/assets/vendor/bootstrap/bootstrap.min.js') !!}" type="text/javascript"></script>
<script src="{!! public_asset('/assets/vendor/bootstrap/bootstrap-notify.js') !!}"></script>
<script src="{!! public_asset('/assets/vendor/chartist/chartist.min.js') !!}"></script>
<script src="{!! public_asset('/assets/vendor/select2/dist/js/select2.js') !!}"></script>
<script src="{!! public_asset('/assets/vendor/pjax/jquery.pjax.js') !!}"></script>
<script src="{!! public_asset('/assets/vendor/icheck/icheck.js') !!}"></script>
<script src="{!! public_asset('/assets/vendor/rivets/dist/rivets.bundled.min.js') !!}"></script>
<script src="https://unpkg.com/leaflet@1.2.0/dist/leaflet.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
--}}


<script src="{!! public_asset('/assets/admin/js/vendor.js') !!}"></script>
<script src="{!! public_asset('/assets/system/js/system.js') !!}"></script>
<script src="{!! public_asset('/assets/admin/js/admin.js') !!}"></script>

<script>
var getStorage = function(key) {
    var st = window.localStorage.getItem(key);
    console.log('storage: ', key, st);
    if(_.isNil(st)) {
        return {
            "menu": [],
        };
    }

    return JSON.parse(st);
};

var saveStorage = function(key, obj) {
    console.log('save: ', key, obj);
    window.localStorage.setItem(key, JSON.stringify(obj));
};

var addItem = function(obj, item) {
    if (_.isNil(obj)) {
        obj = [];
    }

    var index = _.indexOf(obj, item);
    if(index === -1) {
        obj.push(item);
    }

    return obj;
};

var removeItem = function (obj, item) {
    if (_.isNil(obj)) {
        obj = [];
    }
    var index = _.indexOf(obj, item);
    if (index !== -1) {
        console.log("removing", item);
        obj.splice(index, 1);
    }

    return obj;
};

$(document).ready(function () {

    $(".select2").select2();

    $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'icheckbox_square-blue'
    });

    var storage = getStorage("phpvms.admin");

    // see what menu items should be open
    for(var idx = 0; idx < storage.menu.length; idx++) {
        var id = storage.menu[idx];
        var elem = $(".collapse#" + id);
        elem.addClass("in").trigger("show.bs.collapse");

        var caret = $("a." + id + " b");
        caret.addClass("pe-7s-angle-down");
        caret.removeClass("pe-7s-angle-up");
    }

    $(".collapse").on("hide.bs.collapse", function () {
        console.log('hiding');
        var id = $(this).attr('id');
        var elem = $("a." + id + " b");
        elem.removeClass("pe-7s-angle-down");
        elem.addClass("pe-7s-angle-up");

        removeItem(storage.menu, id);
        saveStorage("phpvms.admin", storage);

    });

    $(".collapse").on("show.bs.collapse", function () {
        console.log('showing');
        var id = $(this).attr('id');
        var caret = $("a." + id + " b");
        caret.addClass("pe-7s-angle-down");
        caret.removeClass("pe-7s-angle-up");

        addItem(storage.menu, id);
        saveStorage("phpvms.admin", storage);
    });

});
</script>
@yield('scripts')
</html>
