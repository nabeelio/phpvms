/**
 * admin functions, mostly map/mapping related
 */

function phpvms_vacentral_airport_lookup(icao, callback) {
    $.ajax({
        url: '/api/airports/' + icao + '/lookup',
        method: 'GET'
    }).done(function (data, status) {
        callback(data.data);
    });
}

