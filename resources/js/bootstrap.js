/**
 * Bootstrap any Javascript libraries required
 */

window._ = require('lodash');
window.Popper = require('popper.js').default;
window.$ = window.jquery = require('jquery');
window.select2 = require('select2');
window.pjax = require('pjax');
window.axios = require('axios');

/**
 * Container for phpVMS specific functions
 */
window.phpvms = {

};

/**
 * Configure Axios with both the csrf token and the API key
 */
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    window.jquery.ajaxSetup({
        'X-CSRF-TOKEN': token.content
    })
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

const api_key = document.head.querySelector('meta[name="api-key"]');
if(api_key) {
    window.axios.defaults.headers.common['x-api-key'] = api_key.content;
    window.PHPVMS_USER_API_KEY = api_key.content;
} else {
    window.PHPVMS_USER_API_KEY = false;
    console.error('API Key not found!');
}
