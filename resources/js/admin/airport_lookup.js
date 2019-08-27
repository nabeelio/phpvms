'use strict';

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

    const response = await axios(params);
    console.log('lookup raw response: ', response);
    return response.data;
};
