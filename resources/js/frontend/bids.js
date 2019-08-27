'use strict';

/**
 * Add a bid to a flight
 *
 * @param {String} flight_id
 *
 * @returns {Promise<*>}
 */
export async function addBid(flight_id) {
    const params = {
        method: 'POST',
        url: '/api/user/bids',
        data: {
            '_method': 'POST',
            'flight_id': flight_id
        }
    };

    return axios(params);
}

/**
 * Remove a bid from a given flight
 *
 * @param {String} flight_id
 *
 * @returns {Promise<*>}
 */
export async function removeBid(flight_id) {
    const params = {
        method: 'POST',
        url: '/api/user/bids',
        data: {
            '_method': 'DELETE',
            'flight_id': flight_id
        }
    };

    return axios(params);
}
