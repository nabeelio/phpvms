'use strict';

const axios = require('axios');

import config from './config';

/**
 * Run an API request, with some common options
 *
 * @param {Object|String} opts Axios request options, or pass a URL
 * @param {String} opts.url
 */
export default async (opts) => {
    if (typeof opts === 'string' || opts instanceof String) {
        opts = {
            url: opts,
        };
    }

    const _opts = Object.assign({}, {
        baseURL: config.base_url,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': config.csrf_token,
            'x-api-key': config.api_key,
        }
    }, opts);

    return axios.request(_opts);
};
