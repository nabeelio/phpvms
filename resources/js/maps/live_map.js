'use strict';

const geolib = require('geolib');
const leaflet = require('leaflet');
const rivets = require('rivets');

import draw_base_map from './base_map'
import { ACTUAL_ROUTE_COLOR } from './config'
import request from '../request';

/**
 * Render the live map
 * @param opts
 * @private
 */
export default (opts) => {
    opts = Object.assign({
        center: [29.98139, -95.33374],
        refresh_interval: 10, // seconds
        zoom: 5,
        update_uri: '/api/acars',
        pirep_uri: '/api/pireps/{id}',
        pirep_link_uri: '/pireps/{id}',
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

    /**
     * Hold the markers
     * @type {{}}
     */
    let markers_list = {};
    let pannedToCenter = false;
    let layerFlights = null;
    let layerSelFlight = null;
    let layerSelFlightFeature = null;
    let layerSelFlightLayer = null;

    /**
     * Controller for two-way bindings
     * @type {{focusMarker: focusMarker}}
     */
    const mapController = {
        /**
         * Focus on a specific marker
         * @param e
         * @param model
         */
        focusMarker: (e, model) => {
            if(!(model.pirep.id in markers_list)) {
                console.log('marker not found in list');
                return;
            }

            const marker = markers_list[model.pirep.id];
            onFlightClick(marker[0], marker[1]);
        },
    };

    const r_map_view = rivets.bind($('#map-info-box'), {pirep: {}, controller: mapController});
    const r_table_view = rivets.bind($('#live_flights'), {pireps: [], controller: mapController});

    /**
     * When a flight is clicked on, show the path, etc for that flight
     * @param feature
     * @param layer
     */
    const onFlightClick = (feature, layer) => {
        const pirep_uri = opts.pirep_uri.replace('{id}', feature.properties.pirep_id);
        const geojson_uri = opts.pirep_uri.replace('{id}', feature.properties.pirep_id) + "/acars/geojson";

        /*
         * Get information about the PIREP and populate the bottom box/container
         */
        request(pirep_uri).then(response => {
            const pirep = response.data.data;
            console.log(pirep);

            r_map_view.update({ pirep });
            $('#map-info-box').show();
        });

        /*
         * Draw out the flight route
         */
        request(geojson_uri).then(response => {
            const rte = response.data.data;
            console.log(rte);

            if (layerSelFlight !== null) {
                map.removeLayer(layerSelFlight);
            }

            layerSelFlight = leaflet.geodesic([], {
                weight: 5,
                opacity: 0.9,
                color: ACTUAL_ROUTE_COLOR,
                wrap: false,
            }).addTo(map);

            layerSelFlight.geoJson(rte.line);
            layerSelFlightFeature = feature;
            layerSelFlightLayer = layer;

            // Center on it, but only do it once, in case the map is moved
            if (!pannedToCenter) {
                // find center
                const c = geolib.getCenter([
                    {latitude: rte.airports.a.lat, longitude: rte.airports.a.lon},
                    {latitude: rte.airports.d.lat, longitude: rte.airports.d.lon},
                ]);

                //map.panTo({lat: c.latitude, lng: c.longitude});
                map.panTo({lat: rte.position.lat, lng: rte.position.lon});
                pannedToCenter = true;
            }
        });
    };

    const updateMap = () => {
        const pirep_uri = opts.pirep_uri.replace('{id}', '');

        request(pirep_uri ).then(response => {
            const pireps = response.data.data;
            r_table_view.update({
                pireps,
                has_data: (pireps.length > 0),
            });
        });

        request({ url: opts.update_uri }).then(response => {
            const flightGeoJson = response.data.data;

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

                    // add to the list
                    markers_list[feature.properties.pirep_id] = [feature, layer];
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
        });
    };

    updateMap();
    setInterval(updateMap, opts.refresh_interval * 1000)
};
