const leaflet = require('leaflet');

import draw_base_map from './base_map'
import {ACTUAL_ROUTE_COLOR} from './config'

/**
 * Render the live map
 * @param opts
 * @private
 */
export default (opts) => {

    opts = Object.assign({
        update_uri: '/api/acars',
        pirep_uri: '/api/pireps/{id}',
        positions: null,
        render_elem: 'map',
        aircraft_icon: '/assets/img/acars/aircraft.png',
        units: 'nmi',
    }, opts);

    const map = draw_base_map(opts);
    const aircraftIcon = leaflet.icon({
        iconUrl: opts.aircraft_icon,
        iconSize: [42, 42],
        iconAnchor: [21, 21],
    });

    let pannedToCenter = false;
    let layerFlights = null;
    let layerSelFlight = null;
    let layerSelFlightFeature = null;
    let layerSelFlightLayer = null;

    /**
     * When a flight is clicked on, show the path, etc for that flight
     * @param feature
     * @param layer
     */
    const onFlightClick = (feature, layer) => {

        const pirep_uri = opts.pirep_uri.replace('{id}', feature.properties.pirep_id);
        const geojson_uri = opts.pirep_uri.replace('{id}', feature.properties.pirep_id) + "/acars/geojson";

        const pirep_info = $.ajax({
            url: pirep_uri,
            dataType: 'json',
            error: console.log
        });

        const flight_route = $.ajax({
            url: geojson_uri,
            dataType: 'json',
            error: console.log
        });

        // Load up the PIREP info
        $.when(flight_route).done((routeJson) => {
            if (layerSelFlight !== null) {
                map.removeLayer(layerSelFlight);
            }

            layerSelFlight = leaflet.geodesic([], {
                weight: 7,
                opacity: 0.9,
                color: ACTUAL_ROUTE_COLOR,
                wrap: false,
            }).addTo(map);

            layerSelFlight.geoJson(routeJson.line);
            layerSelFlightFeature = feature;
            layerSelFlightLayer = layer;

            // Center on it, but only do it once, in case the map is moved
            if(!pannedToCenter) {
                map.panTo({lat: routeJson.position.lat, lng: routeJson.position.lon});
                pannedToCenter = true;
            }
        });

        //
        // When the PIREP info is done loading, show the bottom bar
        //
        $.when(pirep_info).done(pirep => { pirep = pirep.data;

            let dist, planned_dist;
            if(opts.units === 'nmi') {
                dist = pirep.distance.nmi;
                planned_dist = pirep.planned_distance.nmi;
            } else if(opts.units === 'mi') {
                dist = pirep.distance.mi;
                planned_dist = pirep.planned_distance.mi;
            } else if(opts.units === 'km') {
                dist = pirep.distance.km;
                planned_dist = pirep.planned_distance.km;
            }

            // Parse flight time
            const hours = Math.floor(pirep.flight_time / 60);
            const mins = pirep.flight_time % 60;

            $('#map_flight_id').text(pirep.airline.icao + pirep.flight_number);
            $('#map_flight_info').text(
                pirep.dpt_airport.name + ' (' + pirep.dpt_airport.icao + ') to ' +
                pirep.arr_airport.name + ' (' + pirep.arr_airport.icao + ')'
            );

            $('#map_flight_stats_middle').html(
                'Status: <strong>' + pirep.status_text + '</strong><br />' +
                'Flight Time: <strong>' + hours + 'h ' + mins + 'm</strong><br />' +
                'Distance: <strong>' + dist + '</strong> / ' + planned_dist + opts.units + '<br />'
            );

            // Show flight stat info
            $('#map_flight_stats_right').html(
                'Ground Speed: <strong>' + pirep.position.gs + '</strong><br />' +
                'Altitude: <strong>' + pirep.position.altitude + '</strong><br />'  +
                'Heading: <strong>' + pirep.position.heading + '</strong>'
            );

            $('#map-info-bar').show();
        });
    };

    const updateMap = () => {

        console.log('reloading flights from acars...');

        /**
         * AJAX UPDATE
         */

        let flights = $.ajax({
            url: opts.update_uri,
            dataType: 'json',
            error: console.log
        });

        $.when(flights).done(function (flightGeoJson) {

            if (layerFlights !== null) {
                layerFlights.clearLayers()
            }

            layerFlights = leaflet.geoJSON(flightGeoJson, {
                onEachFeature: (feature, layer) => {
                    layer.on({
                        click: (e) => {
                            pannedToCenter = false;
                            onFlightClick(feature, layer)
                        }
                    });

                    let popup_html = '';
                    if (feature.properties && (feature.properties.popup !== '' && feature.properties.popup !== undefined)) {
                        popup_html += feature.properties.popup;
                        layer.bindPopup(popup_html);
                    }
                },
                pointToLayer: function (feature, latlon) {
                    return leaflet.marker(latlon, {
                        icon: aircraftIcon,
                        rotationAngle: feature.properties.heading
                    })
                }
            });

            layerFlights.addTo(map);

            // Reload the clicked-flight information
            if (layerSelFlight !== null) {
                onFlightClick(layerSelFlightFeature, layerSelFlightLayer)
            }
        })
    };

    updateMap();
    setInterval(updateMap, 10000)
};
