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
 * ADMIN REQUIRED FILES
 */

mix.sass('public/assets/admin/vendor/sass/paper-dashboard.scss',
         'public/assets/admin/vendor/paper-dashboard.css')
   .sourceMaps();

mix.styles([
        'public/assets/admin/vendor/bootstrap.css',
        'public/assets/admin/vendor/bootstrap-editable.css',
        'public/assets/admin/vendor/animate.css',
        'public/assets/admin/vendor/pe-icon-7-stroke.css',
        'public/assets/admin/vendor/themify-icons.css',
        'public/assets/admin/vendor/font-awesome.css',
        'public/assets/admin/vendor/leaflet.css',
        'public/assets/admin/vendor/paper-dashboard.css',
        'public/assets/vendor/select2/dist/css/select2.css',
        'public/assets/vendor/icheck/skins/flat/orange.css'
    ], 'public/assets/admin/css/vendor.min.css').version()
   .sourceMaps();

mix.scripts([
    'node_modules/lodash/lodash.js',
    'node_modules/jquery/dist/jquery.js',
    'node_modules/popper.js/dist/umd/popper.js',
    'node_modules/popper.js/dist/umd/popper-utils.js',
    'node_modules/select2/dist/js/select2.js',
    'node_modules/leaflet/dist/leaflet.js',
    'node_modules/pjax/pjax.js',
], 'public/assets/admin/js/vendor.js');

/*mix.webpackConfig({
    resolve: {
        alias: {
            jquery: "public/assets/vendor/jquery/dist/jquery.js"
        }
    }
});*/


/**
 * SYSTEM REQUIRED FILES
 */


mix.autoload({
    'jquery': ['jQuery', '$'],
});

mix.scripts([
    'node_modules/lodash/lodash.js',
    'node_modules/jquery/dist/jquery.js',
    'public/assets/vendor/bootstrap/bootstrap.min.js',
    'node_modules/popper.js/dist/umd/popper.js',
    'node_modules/popper.js/dist/umd/popper-utils.js',
    'node_modules/select2/dist/js/select2.js',
    'node_modules/leaflet/dist/leaflet.js',
    'node_modules/pjax/pjax.js',
    'public/assets/vendor/leaflet-plugins/leaflet.geodesic.js',
    'public/assets/vendor/leaflet-plugins/leaflet.rotatedMarker.js',
    'public/assets/vendor/leaflet-plugins/leaflet.rotatedMarker.js',
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
 * DEFAULT SKIN FRONTEND FILES
 */

mix.sass('public/assets/frontend/sass/now-ui-kit.scss',
    'public/assets/frontend/css/now-ui-kit.css')
    .options({
        processCssUrls: false,
        compressed: true
    })
    .sourceMaps();
