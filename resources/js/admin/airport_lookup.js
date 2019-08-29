'use strict';

import request from '../request';

/**
 * Lookup an airport from the server
 *
 * @param {String} icao
 */
export default async (icao) => {
    let params = {
        method: 'GET',
        url: '/api/airports/' + icao + '/lookup',
    };

    const response = await request(params);
    console.log('lookup raw response: ', response);
    return response.data;
};
