
import config from './config';

const axios = require('axios');

/**
 * Run an API request, with some common options
 *
 * @param {Object|String} _opts Axios request options, or pass a URL
 * @param {String} _opts.url
 */
export default async (_opts) => {
  if (typeof _opts === 'string' || _opts instanceof String) {
    // eslint-disable-next-line no-param-reassign
    _opts = {
      url: _opts,
    };
  }

  const opts = Object.assign({}, {
    baseURL: config.base_url,
    headers: {
      'X-API-KEY': config.api_key,
      'X-CSRF-TOKEN': config.csrf_token,
      'x-api-key': config.api_key,
    },
  }, _opts);

  return axios.request(opts);
};
