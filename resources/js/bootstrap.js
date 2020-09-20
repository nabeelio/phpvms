/**
 * Before you edit these, read the documentation on how these files are compiled:
 * https://docs.phpvms.net/developers/building-assets
 *
 * Edits here don't take place until you compile these assets and then upload them.
 */

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
