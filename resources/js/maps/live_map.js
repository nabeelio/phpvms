//

const geolib = require('geolib');
const leaflet = require('leaflet');
const rivets = require('rivets');

import draw_base_map from './base_map'
import { ACTUAL_ROUTE_COLOR } from './config'

/**
 * Render the live map
 * @param opts
 * @private
 */
export default (opts) => {

    opts = Object.assign({
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
    let layerSelArr = null;
    let layerSelDep = null;

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
        $.when(flight_route).done((rte) => {
            if (layerSelFlight !== null) {
                map.removeLayer(layerSelFlight);
                //map.removeLayer(layerSelArr);
                //map.removeLayer(layerSelDep);
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

            /*const dptIcon = leaflet.divIcon({
                html: '<div class="map-info-label"><h5>' + rte.airports.d.icao + '</h5></div>'
            });

            layerSelDep = leaflet.marker([rte.airports.d.lat, rte.airports.d.lon], {icon:dptIcon}).addTo(map);
            */

            // Center on it, but only do it once, in case the map is moved
            if(!pannedToCenter) {
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

        //
        // When the PIREP info is done loading, show the bottom bar
        //
        $.when(pirep_info).done(pirep => {
            r_map_view.update({pirep:pirep.data});
            $('#map-info-box').show();
        });
    };

    const updateMap = () => {

        console.log('reloading flights from acars...');

        /**
         * AJAX UPDATE
         */
        const pirep_uri = opts.pirep_uri.replace('{id}', '');
        let pireps = $.ajax({
            url: pirep_uri,
            dataType: 'json',
            error: console.log
        });

        let flights = $.ajax({
            url: opts.update_uri,
            dataType: 'json',
            error: console.log
        });

        $.when(flights).done(flightGeoJson => {

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

        $.when(pireps).done(pireps => {
            r_table_view.update({
                pireps: pireps.data,
                has_data: (pireps.data.length > 0),
            });
        });
    };

    updateMap();
    setInterval(updateMap, 10000)
};
