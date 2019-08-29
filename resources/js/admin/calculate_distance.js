'use strict';

import request from '../request';

/**
 * Lookup an airport from the server
 *
 * @param {String} fromICAO
 * @param {String} toICAO
 */
export default async (fromICAO, toICAO) => {
    let params = {
        method: 'GET',
        url: '/api/airports/' + fromICAO + '/distance/' + toICAO,
    };

    const response = await request(params);
    console.log('distance raw response: ', response);
    return response.data;
};
