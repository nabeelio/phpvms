/**
 *
 * @type {{render_airspace_map, render_live_map, render_route_map}}
 */

const phpvms = (function() {

    const PLAN_ROUTE_COLOR = '#36b123';
    const ACTUAL_ROUTE_COLOR = '#172aea';

    const draw_base_map = (opts) => {

        opts = _.defaults(opts, {
            render_elem: 'map',
            center: [29.98139, -95.33374],
            zoom: 5,
            maxZoom: 10,
            layers: [],
            set_marker: false,
        });

        let feature_groups = [];
        /*var openaip_airspace_labels = new L.TileLayer.WMS(
            "http://{s}.tile.maps.openaip.net/geowebcache/service/wms", {
                maxZoom: 14,
                minZoom: 12,
                layers: 'openaip_approved_airspaces_labels',
                tileSize: 1024,
                detectRetina: true,
                subdomains: '12',
                format: 'image/png',
                transparent: true
            });

        openaip_airspace_labels.addTo(map);*/

        const opencyclemap_phys_osm = new L.TileLayer(
            'http://{s}.tile.thunderforest.com/landscape/{z}/{x}/{y}.png?apikey=f09a38fa87514de4890fc96e7fe8ecb1', {
                maxZoom: 14,
                minZoom: 4,
                format: 'image/png',
                transparent: true
            });

        feature_groups.push(opencyclemap_phys_osm);

        /*const openaip_cached_basemap = new L.TileLayer("http://{s}.tile.maps.openaip.net/geowebcache/service/tms/1.0.0/openaip_basemap@EPSG%3A900913@png/{z}/{x}/{y}.png", {
            maxZoom: 14,
            minZoom: 4,
            tms: true,
            detectRetina: true,
            subdomains: '12',
            format: 'image/png',
            transparent: true
        });

        feature_groups.push(openaip_cached_basemap);
        */

        const openaip_basemap_phys_osm = L.featureGroup(feature_groups);

        let map = L.map('map', {
            layers: [openaip_basemap_phys_osm],
            center: opts.center,
            zoom: opts.zoom,
            scrollWheelZoom: false,
        });

        const attrib = L.control.attribution({position: 'bottomleft'});
        attrib.addAttribution("<a href=\"https://www.thunderforest.com\" target=\"_blank\" style=\"\">Thunderforest</a>");
        attrib.addAttribution("<a href=\"https://www.openaip.net\" target=\"_blank\" style=\"\">openAIP</a>");
        attrib.addAttribution("<a href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\" style=\"\">OpenStreetMap</a> contributors");
        attrib.addAttribution("<a href=\"https://www.openweathermap.org\" target=\"_blank\" style=\"\">OpenWeatherMap</a>");

        attrib.addTo(map);

        return map;
    };


    /**
     * Show some popup text when a feature is clicked on
     * @param feature
     * @param layer
     */
    const onFeaturePointClick = (feature, layer) => {
        let popup_html = "";
        if (feature.properties && feature.properties.popup) {
            popup_html += feature.properties.popup;
        }

        layer.bindPopup(popup_html);
    };

    /**
     * Show each point as a marker
     * @param feature
     * @param latlng
     * @returns {*}
     */
    const pointToLayer = (feature, latlng) => {
        return L.circleMarker(latlng, {
            radius: 12,
            fillColor: "#ff7800",
            color: "#000",
            weight: 1,
            opacity: 1,
            fillOpacity: 0.8
        });
    };

    /**
     *
     * @param opts
     * @private
     */
    const _render_route_map = (opts) => {

        opts = _.defaults(opts, {
            route_points: null,
            planned_route_line: null,
            actual_route_points: null,
            actual_route_line: null,
            render_elem: 'map',
        });

        let map = draw_base_map(opts);

        let geodesicLayer = L.geodesic([], {
            weight: 7,
            opacity: 0.9,
            color: PLAN_ROUTE_COLOR,
            steps: 50,
            wrap: false,
        }).addTo(map);

        geodesicLayer.geoJson(opts.planned_route_line);
        map.fitBounds(geodesicLayer.getBounds());

        // Draw the route points after
        if (opts.route_points !== null) {
            let route_points = L.geoJSON(opts.route_points, {
                onEachFeature: onFeaturePointClick,
                pointToLayer: pointToLayer,
                style: {
                    "color": PLAN_ROUTE_COLOR,
                    "weight": 5,
                    "opacity": 0.65,
                },
            });

            route_points.addTo(map);
        }

        /**
         * draw the actual route
         */

        if (opts.actual_route_line !== null) {
            let geodesicLayer = L.geodesic([], {
                weight: 7,
                opacity: 0.9,
                color: ACTUAL_ROUTE_COLOR,
                steps: 50,
                wrap: false,
            }).addTo(map);

            geodesicLayer.geoJson(opts.actual_route_line);
            map.fitBounds(geodesicLayer.getBounds());
        }

        if (opts.actual_route_points !== null) {
            let route_points = L.geoJSON(opts.actual_route_points, {
                onEachFeature: onFeaturePointClick,
                pointToLayer: pointToLayer,
                style: {
                    "color": ACTUAL_ROUTE_COLOR,
                    "weight": 5,
                    "opacity": 0.65,
                },
            });

            route_points.addTo(map);
        }
    };

    /**
     * Render a map with the airspace, etc around a given set of coords
     * e.g, the airport map
     * @param opts
     */
    const _render_airspace_map = (opts) => {
        opts = _.defaults(opts, {
            render_elem: 'map',
            overlay_elem: '',
            lat: 0,
            lon: 0,
            zoom: 12,
            layers: [],
            set_marker: false,
        });

        let map = draw_base_map(opts);
        const coords = [opts.lat, opts.lon];
        console.log('Applying coords', coords);

        map.setView(coords, opts.zoom);
        if (opts.set_marker === true) {
            L.marker(coords).addTo(map);
        }

        return map;
    };

    /**
     * Render the live map
     * @param opts
     * @private
     */
    const _render_live_map = (opts) => {

        opts = _.defaults(opts, {
            update_uri: '/api/acars',
            pirep_uri: '/api/pireps/{id}/acars',
            positions: null,
            render_elem: 'map',
            aircraft_icon: '/assets/img/acars/aircraft.png',
        });

        const map = draw_base_map(opts);
        const aircraftIcon = L.icon({
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
            console.log('flight check uri', uri);

            const flight_route = $.ajax({
                url: uri,
                dataType: "json",
                error: console.log
            });

            $.when(flight_route).done((routeJson) => {
                if(layerSelFlight !== null) {
                    map.removeLayer(layerSelFlight);
                }

                layerSelFlight = L.geodesic([], {
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

                layerFlights = L.geoJSON(flightGeoJson, {
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
                    pointToLayer: function(feature, latlon) {
                        return L.marker(latlon, {
                            icon: aircraftIcon,
                            rotationAngle: feature.properties.heading
                        });
                    }
                });

                layerFlights.addTo(map);

                if (layerSelFlight !== null) {
                    onFlightClick(layerSelFlightFeature, layerSelFlightLayer);
                }
                //map.fitBounds(layerFlights.getBounds());
                //map.fitBounds('39.8283° N, 98.5795° W', 40);
            });
        };

        updateMap();
        setInterval(updateMap, 10000);
    };

    return {
        render_airspace_map: _render_airspace_map,
        render_live_map: _render_live_map,
        render_route_map: _render_route_map,
    }
})();
