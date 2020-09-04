/**
 * Before you edit these, read the documentation on how these files are compiled:
 * https://docs.phpvms.net/customize/building-assets
 *
 * Edits here don't take place until you compile these assets and then upload them.
 */

const leaflet = require('leaflet');
require('leaflet-providers');

export default (_opts) => {
  const opts = Object.assign({
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
  }, _opts);

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

  openaip_airspace_labels.addTo(map); */

  /*
  const openaip_cached_basemap = new leaflet.TileLayer("http://{s}.tile.maps.openaip.net/geowebcache/service/tms/1.0.0/openaip_basemap@EPSG%3A900913@png/{z}/{x}/{y}.png", {
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

  const map = leaflet.map('map', {
    // layers: [openaip_basemap_phys_osm],
    center: opts.center,
    zoom: opts.zoom,
    scrollWheelZoom: false,
  });

  // eslint-disable-next-line no-unused-vars
  opts.providers.forEach((p, idx) => {
    leaflet.tileLayer.provider(p).addTo(map);
  });

  return map;
};
