/**
 * Bootstrap any Javascript libraries required
 */

window._ = require('lodash');
window.Popper = require('popper.js').default;
window.$ = window.jquery = require('jquery');
window.select2 = require('select2');
window.pjax = require('pjax');

// Container for phpVMS specific functions
window.phpvms = {

};

/**
 * Configure Axios
 */
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
