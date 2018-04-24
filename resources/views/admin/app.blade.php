<!doctype html>
<html lang="en">
<head>
    <title>@yield('title') - {{ config('app.name') }} admin</title>

    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />

    {{-- Start of required lines block. DON'T REMOVE THESE LINES! They're required or might break things --}}
    <meta name="base-url" content="{{ url('') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-key" content="{{ Auth::check() ? Auth::user()->api_key: '' }}">
    {{-- End the required lines block --}}

    <script src="{{ public_asset('/assets/global/js/jquery.js') }}"></script>

    <link rel="shortcut icon" type="image/png" href="{{ public_asset('/assets/img/favicon.png') }}"/>

    <link href='https://fonts.googleapis.com/css?family=Muli:400,300' rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700,300" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="{{ public_asset('/assets/global/css/vendor.css') }}">
    <link rel="stylesheet" href="{{ public_asset('/assets/admin/css/vendor.css') }}">
    <link rel="stylesheet" href="{{ public_asset('/assets/admin/css/admin.css') }}">

    <style type="text/css">
    @yield('css')
    </style>

    <script>
        const BASE_URL ='{{ url('/') }}';
    @if (Auth::user())
        const PHPVMS_USER_API_KEY = "{{ Auth::user()->api_key }}";
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
                        @include('admin.flash.message')
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer">
            <div class="container-fluid">
                <nav class="pull-left">
                    <ul>
                    </ul>
                </nav>
                <div class="copyright pull-right">
                    powered by <a href="http://www.phpvms.net" target="_blank">phpvms</a>
                    @version
                </div>
            </div>
        </footer>
    </div>
</div>
</body>

<script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
<script defer src="{{ public_asset('/assets/admin/js/vendor.js') }}"></script>
<script defer src="{{ public_asset('/assets/admin/js/app.js') }}"></script>

<script>
const getStorage = function(key) {
    const st = window.localStorage.getItem(key);
    // console.log('storage: ', key, st);
    if(!st) {
        return {
            "menu": [],
        };
    }

    return JSON.parse(st);
};

const saveStorage = function(key, obj) {
    // console.log('save: ', key, obj);
    window.localStorage.setItem(key, JSON.stringify(obj));
};

const addItem = function(obj, item) {
    if (!obj) { obj = []; }
    const index = obj.indexOf(item);
    if(index === -1) {
        obj.push(item);
    }

    return obj;
};

const removeItem = function (obj, item) {
    if (!obj) { obj = []; }
    const index = obj.indexOf(item);
    if (index !== -1) {
        obj.splice(index, 1);
    }

    return obj;
};

/**
 * Initialize any plugins on the page
 */
const initPlugins = () => {
  $('.select2').select2({width: 'resolve'});
  $('input').iCheck({
    checkboxClass: 'icheckbox_square-blue',
    radioClass: 'icheckbox_square-blue'
  });
};

$(document).ready(function () {

    initPlugins();

    let storage = getStorage('phpvms.admin');

    // see what menu items should be open
    for(let idx = 0; idx < storage.menu.length; idx++) {
        const id = storage.menu[idx];
        const elem = $(".collapse#" + id);
        elem.addClass("in").trigger("show.bs.collapse");

        const caret = $("a." + id + " b");
        caret.addClass("pe-7s-angle-down");
        caret.removeClass("pe-7s-angle-right");
    }

    $(".collapse").on("hide.bs.collapse", function () {
        // console.log('hiding');
        const id = $(this).attr('id');
        const elem = $("a." + id + " b");
        elem.removeClass("pe-7s-angle-down");
        elem.addClass("pe-7s-angle-right");

        removeItem(storage.menu, id);
        saveStorage("phpvms.admin", storage);
    });

    $(".collapse").on("show.bs.collapse", function () {
        // console.log('showing');
        const id = $(this).attr('id');
        const caret = $("a." + id + " b");
        caret.addClass("pe-7s-angle-down");
        caret.removeClass("pe-7s-angle-right");

        addItem(storage.menu, id);
        saveStorage("phpvms.admin", storage);
    });
});
</script>

@yield('scripts')

</html>
