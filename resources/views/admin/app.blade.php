<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="/assets/frontend/img/favicon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

    <title>@yield('title') - phpvms admin</title>

    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />

    <link href="/assets/admin/css/bootstrap.css" rel="stylesheet" />
    <link href="/assets/admin/css/animate.min.css" rel="stylesheet"/>
    <link href="/assets/admin/css/paper-dashboard.css" rel="stylesheet"/>

    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css"
          rel="stylesheet"/>

    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Muli:400,300' rel='stylesheet' type='text/css'>
    <link href="http://fonts.googleapis.com/css?family=Roboto:400,700,300" rel="stylesheet"
          type="text/css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.2.0/dist/leaflet.css"/>
    <link href="/assets/admin/css/pe-icon-7-stroke.css" rel="stylesheet">
    <link href="/assets/admin/css/themify-icons.css" rel="stylesheet">
    <link href="/assets/vendor/select2/dist/css/select2.min.css" rel="stylesheet">
    <link href="/assets/vendor/icheck/skins/flat/orange.css" rel="stylesheet">

    <style type="text/css">
        /*.card {
            display: inline-block;
            position: relative;
            overflow: hidden;
            width: 100%;
            margin-bottom: 20px;
            !*box-shadow: 0px 5px 25px 0px rgba(0, 0, 0, 0.2);*!
        }*/

        .border-blue-bottom {
            border-bottom: 3px solid #067ec1;
        }
    @yield('css')
    </style>

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

<script src="https://cdn.jsdelivr.net/lodash/4.17.4/lodash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="/assets/admin/js/bootstrap.min.js" type="text/javascript"></script>
<script src="/assets/admin/js/chartist.min.js"></script>
<script src="/assets/admin/js/bootstrap-notify.js"></script>
<script src="/assets/vendor/select2/dist/js/select2.js"></script>
<script src="/assets/vendor/pjax/jquery.pjax.js"></script>
<script src="/assets/vendor/icheck/icheck.js"></script>
<script src="/assets/vendor/rivets/dist/rivets.bundled.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<script src="https://unpkg.com/leaflet@1.2.0/dist/leaflet.js"></script>

<script src="/js/admin/admin.js"></script>

<script>
rivets.configure({
    prefix: 'rv',
    preloadData: true,
    rootInterface: '.',
    templateDelimiters: ['{', '}'],
    iterationAlias: function (modelName) {
        return '%' + modelName + '%';
    },
    // Augment the event handler of the on-* binder
    handler: function (target, event, binding) {
        this.call(target, event, binding.view.models)
    },
    executeFunctions: false

});

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
        checkboxClass: 'icheckbox_flat-orange',
        radioClass: 'iradio_flat-orange'
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
