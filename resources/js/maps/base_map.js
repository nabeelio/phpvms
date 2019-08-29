//

const leaflet = require('leaflet');
require('leaflet-providers');

export default (opts) => {
    opts = Object.assign({
        render_elem: 'map',
        center: [29.98139, -95.33374],
        zoom: 5,
        maxZoom: 10,
        layers: [],
        set_marker: false,
        providers: [
            'Esri.WorldStreetMap',
        ],
        tile_layers: [],
    }, opts);

    /*
    let feature_groups = [];
    const openaip_airspace_labels = new leaflet.TileLayer.WMS(
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

    /*const openaip_cached_basemap = new leaflet.TileLayer("http://{s}.tile.maps.openaip.net/geowebcache/service/tms/1.0.0/openaip_basemap@EPSG%3A900913@png/{z}/{x}/{y}.png", {
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

    let map = leaflet.map('map', {
        //layers: [openaip_basemap_phys_osm],
        center: opts.center,
        zoom: opts.zoom,
        scrollWheelZoom: false,
    });

    for (const p of opts.providers) {
        leaflet.tileLayer.provider(p).addTo(map);
    }

    return map;
};
