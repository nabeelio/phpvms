/**
 * Before you edit these, read the documentation on how these files are compiled:
 * https://docs.phpvms.net/developers/building-assets
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
    leafletOptions: {
      scrollWheelZoom: false,
    },
  }, _opts);

  const map = leaflet.map('map', Object.assign({
    center: opts.center,
    zoom: opts.zoom,
    scrollWheelZoom: false,
  }, opts.leafletOptions));

  // eslint-disable-next-line no-unused-vars
  opts.providers.forEach((p, idx) => {
    leaflet.tileLayer.provider(p).addTo(map);
  });

  return map;
};
