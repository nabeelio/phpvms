/**
 * Bootstrap any Javascript libraries required
 */

window.axios = require('axios');

/**
 * Container for phpVMS specific functions
 */
window.phpvms = {};

/**
 * Configure Axios with both the csrf token and the API key
 */

const base_url = document.head.querySelector('meta[name="base-url"]');
if(base_url) {
  window.axios.default.baseURL = base_url;
}

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
  window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
  /*window.jquery.ajaxSetup({
    'X-CSRF-TOKEN': token.content
  })*/
} else {
  console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token')
}

const api_key = document.head.querySelector('meta[name="api-key"]');
if (api_key) {
  window.axios.defaults.headers.common['x-api-key'] = api_key.content;
  window.PHPVMS_USER_API_KEY = api_key.content
} else {
  window.PHPVMS_USER_API_KEY = false;
  console.error('API Key not found!')
}

require('./common');
