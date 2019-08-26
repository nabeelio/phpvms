/**
 * Lookup an airport from the server
 * @param fromICAO
 * @param toICAO
 * @param callback
 */
export default (fromICAO, toICAO, callback) => {
    let params = {
        method: 'GET',
        url: '/api/airports/' + fromICAO + '/distance/' + toICAO,
    };

    console.log('Calcuating airport distance');
    axios(params)
        .then(response => {
            console.log(response);
            callback(response.data);
        });
};
