/**
 * Lookup an airport from the server
 * @param icao
 * @param callback
 */
export default (icao, callback) => {
    let params = {
        method: 'GET',
        url: '/api/airports/' + icao + '/lookup',
    };

    console.log('Looking airport up');
    axios(params)
        .then(response => {
            console.log(response);
            callback(response.data);
        });
};
