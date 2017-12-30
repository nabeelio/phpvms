/**
 *
 */

'use strict';

const mix = require('laravel-mix');
const webpack = require('webpack');

mix.webpackConfig({
    plugins: [
        new webpack.ProvidePlugin({
            $: "jquery",
            jQuery: "jquery"
        })
    ]
});

/**
 * GENERAL FILES
 */

mix.copy('node_modules/jquery/dist/jquery.js', 'public/assets/system/js/');
mix.copy('node_modules/bootstrap3/fonts/*.woff2', 'public/assets/fonts/');
mix.copy('node_modules/bootstrap3/fonts/*.woff2', 'public/assets/admin/fonts/');
mix.copy('node_modules/x-editable/dist/bootstrap3-editable/img/*', 'public/assets/admin/css/');

/**
 * ADMIN REQUIRED FILES
 */

mix.sass('public/assets/admin/vendor/sass/paper-dashboard.scss',
         'public/assets/admin/vendor/paper-dashboard.css')
   .sourceMaps();

mix.styles([
    'node_modules/bootstrap3/dist/css/bootstrap.css',
    'public/assets/admin/vendor/animate.css',
    'public/assets/admin/vendor/pe-icon-7-stroke.css',
    'public/assets/admin/vendor/themify-icons.css',
    'public/assets/admin/vendor/font-awesome.css',
    'node_modules/select2/dist/css/select2.css',
    'node_modules/leaflet/dist/leaflet.css',
    'node_modules/icheck/skins/square/blue.css',
    'node_modules/x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css',
    'public/assets/admin/vendor/paper-dashboard.css',
    'node_modules/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css',
], 'public/assets/admin/css/vendor.min.css').version()
   .sourceMaps();

mix.scripts([
    'node_modules/lodash/lodash.js',
    //'node_modules/jquery/dist/jquery.js',  // commented out to include separately
    'node_modules/moment/moment.js',
    'node_modules/bootstrap3/dist/js/bootstrap.js',
    'node_modules/bootstrap3/js/collapse.js',
    'node_modules/bootstrap3/js/transition.js',
    'node_modules/popper.js/dist/umd/popper.js',
    'node_modules/popper.js/dist/umd/popper-utils.js',
    'node_modules/select2/dist/js/select2.js',
    'node_modules/leaflet/dist/leaflet.js',
    'node_modules/icheck/icheck.js',
    // 'node_modules/pjax/pjax.js',
    'node_modules/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.js',
    // 'node_modules/x-editable/dist/bootstrap-datepicker/dist/js/bootstrap-datepicker.js',
    'node_modules/eonasdan-bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js',
    'node_modules/jquery-pjax/jquery.pjax.js',
], 'public/assets/admin/js/vendor.js');

mix.copy('node_modules/icheck/skins/square/blue*.png', 'public/assets/admin/css');

/**
 * SYSTEM REQUIRED FILES
 */

mix.autoload({
    'jquery': ['jQuery', '$'],
});

mix.scripts([
    'node_modules/lodash/lodash.js',
    'node_modules/jquery/dist/jquery.js',
    'node_modules/bootstrap3/dist/js/bootstrap.js',
    'node_modules/popper.js/dist/umd/popper.js',
    'node_modules/popper.js/dist/umd/popper-utils.js',
    'node_modules/select2/dist/js/select2.js',
    'node_modules/leaflet/dist/leaflet.js',
    'node_modules/pjax/pjax.js',
    'node_modules/Leaflet.Geodesic/Leaflet.Geodesic.js',
    'node_modules/leaflet-rotatedmarker/leaflet.rotatedMarker.js',
    /*'public/assets/frontend/js/plugins/bootstrap-switch.js',
    'public/assets/frontend/js/plugins/nouislider.min.js',
    'public/assets/frontend/js/plugins/bootstrap-datepicker.js',
    'public/assets/frontend/js/now-ui-kit.js',*/
], 'public/assets/system/js/vendor.js');

mix.styles([
    'node_modules/select2/dist/css/select2.css',
    'node_modules/leaflet/dist/leaflet.css',
], 'public/assets/system/css/vendor.css')
    .options({
        processCssUrls: false,
        compressed: true
    })
    .sourceMaps();


/**
 * INSTALLER VENDOR FILES
 */

mix.scripts([
    'node_modules/lodash/lodash.js',
    'node_modules/jquery/dist/jquery.js',
    'node_modules/pjax/pjax.js',
], 'public/assets/system/js/installer-vendor.js');


/**
 * DEFAULT SKIN FRONTEND FILES
 */

mix.sass('public/assets/frontend/sass/now-ui-kit.scss',
         'public/assets/frontend/css/now-ui-kit.css')
    .options({
        processCssUrls: false,
        compressed: true
    })
    .sourceMaps();
