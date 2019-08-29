/**
 * Bootstrap any Javascript libraries required
 */


window.axios = require('axios');

import Storage from './storage';
import config from './config';
import request from './request';

/**
 * Container for phpVMS specific functions
 */
window.phpvms = {
  config,
  request,
  Storage,
};

/**
 * Configure Axios with both the csrf token and the API key
 */

/*const base_url = document.head.querySelector('meta[name="base-url"]');
if(base_url) {
  console.log(`baseURL=${base_url.content}`);
  window.phpvms.config.base_url = config.base_url;
  window.axios.default.baseURL = base_url.content;
}

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
  window.phpvms.config.csrf_token = token.content;
} else {
  console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token')
}

const api_key = document.head.querySelector('meta[name="api-key"]');
if (api_key) {
  window.axios.defaults.headers.common['x-api-key'] = api_key.content;
  window.phpvms.config.user_api_key = config.api_key;
  window.PHPVMS_USER_API_KEY = config.api_key
} else {
  window.phpvms.config.user_api_key = false;
  window.PHPVMS_USER_API_KEY = false;
  console.error('API Key not found!')
}*/

require('./common');
