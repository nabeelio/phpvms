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

    const response = await axios(params);
    console.log('distance raw response: ', response);
    return response.data;
};
