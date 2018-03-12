
const _ = require('lodash');
const leaflet = require('leaflet');

import draw_base_map from './base_map';
import {ACTUAL_ROUTE_COLOR} from './config';

/**
 * Render the live map
 * @param opts
 * @private
 */
export default (opts) => {

    opts = _.defaults(opts, {
        update_uri: '/api/acars',
        pirep_uri: '/api/pireps/{id}/acars',
        positions: null,
        render_elem: 'map',
        aircraft_icon: '/assets/img/acars/aircraft.png',
    });

    const map = draw_base_map(opts);
    const aircraftIcon = leaflet.icon({
        iconUrl: opts.aircraft_icon,
        iconSize: [42, 42],
        iconAnchor: [21, 21],
    });

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

        const uri = opts.pirep_uri.replace('{id}', feature.properties.pirep_id);

        const flight_route = $.ajax({
            url: uri,
            dataType: "json",
            error: console.log
        });

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
            //map.fitBounds(layerSelFlight.getBounds());
        });
    };

    const updateMap = () => {

        console.log('reloading flights from acars...');

        /**
         * AJAX UPDATE
         */

        let flights = $.ajax({
            url: opts.update_uri,
            dataType: "json",
            error: console.log
        });

        $.when(flights).done(function (flightGeoJson) {

            if (layerFlights !== null) {
                layerFlights.clearLayers();
            }

            layerFlights = leaflet.geoJSON(flightGeoJson, {
                onEachFeature: (feature, layer) => {

                    layer.on({
                        click: (e) => {
                            onFlightClick(feature, layer);
                        }
                    });

                    let popup_html = "";
                    if (feature.properties && feature.properties.popup) {
                        popup_html += feature.properties.popup;
                    }

                    layer.bindPopup(popup_html);
                },
                pointToLayer: function (feature, latlon) {
                    return leaflet.marker(latlon, {
                        icon: aircraftIcon,
                        rotationAngle: feature.properties.heading
                    });
                }
            });

            layerFlights.addTo(map);

            if (layerSelFlight !== null) {
                onFlightClick(layerSelFlightFeature, layerSelFlightLayer);
            }
        });
    };

    updateMap();
    setInterval(updateMap, 10000);
};
