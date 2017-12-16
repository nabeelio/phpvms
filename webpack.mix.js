let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.sass('public/assets/admin/vendor/sass/paper-dashboard.scss',
         'public/assets/admin/vendor/paper-dashboard.css')
    .styles([
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
    /*.js([
        'public/js/admin/bootstrap.min.js',
        'public/js/admin/admin.js'
    ], 'public/assets/admin/js/admin.js')
    .sourceMaps();*/
