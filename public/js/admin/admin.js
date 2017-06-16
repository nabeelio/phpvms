/**
 * admin functions, mostly map/mapping related
 */

function phpvms_render_airspace_map(opts) {
    opts = __parse_opts(opts);
    var map = __draw_base_map(opts);
    if(opts.set_marker == true) { L.marker(coords).addTo(map); }
    return map;
}

function __parse_opts(opts) {
    _.defaults(opts, {
        render_elem: 'map',
        overlay_elem: '',
        lat: 0, 
        lon: 0,
        zoom: 12,
        layers: [],
        set_marker: false,
    });

    return opts;
}

function __draw_base_map(opts) {
    
    var coords = [opts.lat, opts.lon];

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

    var opencyclemap_phys_osm = new L.TileLayer(
        'http://{s}.tile.thunderforest.com/landscape/{z}/{x}/{y}.png?apikey=f09a38fa87514de4890fc96e7fe8ecb1', {
            maxZoom: 14,
            minZoom: 4,
            format: 'image/png',
            transparent: true
        });

    var openaip_cached_basemap = new L.TileLayer("http://{s}.tile.maps.openaip.net/geowebcache/service/tms/1.0.0/openaip_basemap@EPSG%3A900913@png/{z}/{x}/{y}.png", {
        maxZoom: 14,
        minZoom: 4,
        tms: true,
        detectRetina: false,
        subdomains: '12',
        format: 'image/png',
        transparent: true
    });

    var openaip_basemap_phys_osm = L.featureGroup([opencyclemap_phys_osm, openaip_cached_basemap]);

    var map = L.map('map', {
        layers: [openaip_basemap_phys_osm],
        center: coords,
        zoom: opts.zoom,
        scrollWheelZoom: false,
    }).setView(coords, opts.zoom);

    var attrib = L.control.attribution({position: 'bottomleft'});
    attrib.addAttribution("<a href=\"https://www.thunderforest.com\" target=\"_blank\" style=\"\">Thunderforest</a>");
    attrib.addAttribution("<a href=\"https://www.openaip.net\" target=\"_blank\" style=\"\">openAIP</a>");
    attrib.addAttribution("<a href=\"https://www.openstreetmap.org/copyright\" target=\"_blank\" style=\"\">OpenStreetMap</a> contributors");
    attrib.addAttribution("<a href=\"https://www.openweathermap.org\" target=\"_blank\" style=\"\">OpenWeatherMap</a>");

    attrib.addTo(map);

    return map;
}