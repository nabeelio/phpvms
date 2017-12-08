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

mix.sass('public/assets/admin/sass/paper-dashboard.scss',
         'public/assets/admin/css/paper-dashboard.css')
    .styles([
        'public/assets/admin/css/bootstrap.min.css',
        'public/assets/admin/css/animate.min.css',
        'public/assets/admin/css/pe-icon-7-stroke.css',
        'public/assets/admin/css/themify-icons.css',
        'public/assets/admin/css/paper-dashboard.css',
        'public/assets/admin/css/demo.css'
    ], 'public/css/admin/admin.css')
    .js([
        'public/js/admin/bootstrap.min.js',
        'public/js/admin/admin.js'
    ], 'public/assets/admin/js/admin.js')
    .sourceMaps();
