'use strict';

const mix = require('laravel-mix');

/**
 * COPY ASSETS
 * Copy required assets
 */

mix.copy('node_modules/bootstrap3/fonts/*.woff2', 'public/assets/fonts/');
mix.copy('node_modules/bootstrap3/fonts/*.woff2', 'public/assets/admin/fonts/');
mix.copy('node_modules/x-editable/dist/bootstrap3-editable/img/*', 'public/assets/admin/img/');
mix.copy('node_modules/jquery/dist/jquery.js', 'public/assets/global/js/');
mix.copy('node_modules/flag-icon-css/flags/', 'public/assets/global/flags/');
mix.copy('node_modules/leaflet/dist/images/', 'public/assets/global/css/images/');

/**
 * DEFAULT SKIN FRONTEND FILES
 */

mix.sass('resources/sass/now-ui/now-ui-kit.scss',
    'public/assets/frontend/css/now-ui-kit.css')
    .options({
        processCssUrls: false,
        compressed: true
    }).sourceMaps();

/**
 * ADMIN REQUIRED FILES
 */

mix.sass('resources/sass/admin/paper-dashboard.scss',
      'public/assets/admin/css/vendor.min.css')
    .styles([
      'node_modules/bootstrap3/dist/css/bootstrap.css',
      'node_modules/animate.css/animate.css',
      'node_modules/icheck/skins/square/blue.css',
      'node_modules/select2/dist/css/select2.css',
      'node_modules/pikaday/css/pikaday.css',
      'node_modules/x-editable/dist/bootstrap3-editable/css/bootstrap-editable.css',
      'node_modules/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css',
      'public/assets/admin/css/vendor.min.css',
    ], 'public/assets/admin/css/vendor.css').version()
    .options({
        compressed: true
    })
    .sourceMaps();

mix.scripts([
  'node_modules/lodash/lodash.js',
  'node_modules/jquery/dist/jquery.js',
  'node_modules/moment/moment.js',
  //'node_modules/axios/dist/axios.js',
  'node_modules/bootstrap3/dist/js/bootstrap.js',
  'node_modules/bootstrap3/js/collapse.js',
  'node_modules/bootstrap3/js/transition.js',
  'node_modules/popper.js/dist/umd/popper.js',
  'node_modules/popper.js/dist/umd/popper-utils.js',
  'node_modules/select2/dist/js/select2.js',
  //'node_modules/leaflet/dist/leaflet.js',
  'node_modules/icheck/icheck.js',
  'node_modules/pikaday/pikaday.js',
  'node_modules/x-editable/dist/bootstrap3-editable/js/bootstrap-editable.js',
  'node_modules/eonasdan-bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js',
  'node_modules/jquery-pjax/jquery.pjax.js',
  'node_modues/paper-dashboard/assets/js/paper-dashboard.js',
], 'public/assets/admin/js/vendor.js');

mix.copy('node_modules/icheck/skins/square/blue*.png', 'public/assets/admin/css');

/**
 * SYSTEM REQUIRED AND GLOBAL VENDOR FILES
 */

mix.scripts([
  'node_modules/lodash/lodash.js',
  //'node_modules/axios/dist/axios.js',
  'node_modules/jquery/dist/jquery.js',
  //'node_modules/bootstrap3/dist/js/bootstrap.js',
  //'node_modules/popper.js/dist/umd/popper.js',
  //'node_modules/popper.js/dist/umd/popper-utils.js',
  'node_modules/select2/dist/js/select2.js',
  //'node_modules/leaflet/dist/leaflet.js',
  //'node_modules/pjax/pjax.js',
  //'node_modules/leaflet-rotatedmarker/leaflet.rotatedMarker.js',
  //'node_modules/Leaflet.Geodesic/Leaflet.Geodesic.js',
], 'public/assets/global/js/vendor.js');

mix.styles([
  'node_modules/select2/dist/css/select2.css',
  'node_modules/leaflet/dist/leaflet.css',
  'node_modules/flag-icon-css/css/flag-icon.css',
], 'public/assets/global/css/vendor.css')
  .options({
    //processCssUrls: true,
    compressed: true
  })
  .sourceMaps();

/**
 * INSTALLER VENDOR FILES
 */

mix.styles([
  'node_modules/bootstrap3/dist/css/bootstrap.css',
  'public/assets/frontend/css/now-ui-kit.css',
  'node_modules/select2/dist/css/select2.css',
  'node_modules/flag-icon-css/css/flag-icon.css',
], 'public/assets/installer/css/vendor.css')
  .options({
    //processCssUrls: false,
    compressed: true
  })
  .sourceMaps();

mix.scripts([
  'node_modules/jquery/dist/jquery.js',
  'node_modules/select2/dist/js/select2.js',
], 'public/assets/installer/js/vendor.js');

/**
 * COMMON JS STUFF
 */

// These should go into the separate vendor.js file
const extract = [
  'lodash',
  'popper.js',
  'jquery',
  'select2',
  'pjax',
  'leaflet',
  'Leaflet.Geodesic',
  'leaflet-rotatedmarker'
];

mix.js('resources/js/frontend/app.js', 'public/assets/frontend/js/app.js');
mix.js('resources/js/installer/app.js', 'public/assets/installer/js/app.js');
mix.js('resources/js/admin/app.js', 'public/assets/admin/js/app.js');

mix.webpackConfig({
    /*entry: {
        admin: __dirname + "/resources/js/admin/app.js",
        app: __dirname + "/resources/js/frontend/app.js",
    },
    output: {
        filename: "[name].js",
        path: __dirname + "/public/js/",
    },*/
    plugins: [
        //new BundleAnalyzerPlugin()
    ]
});
