/**
 * Bootstrap any Javascript libraries required
 */

import Storage from './storage';
import config from './config';
import request from './request';

window.axios = require('axios');

/**
 * Container for phpVMS specific functions
 */
window.phpvms = {
  config,
  request,
  Storage,
};

require('./common');
